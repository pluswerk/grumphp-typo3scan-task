<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Task;

use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Pluswerk\GrumPHPTypo3ScanTask\Paths;
use Pluswerk\GrumPHPTypo3ScanTask\Service\ScannerService;
use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use TYPO3\CMS\Scanner\Domain\Model\DirectoryMatches;
use TYPO3\CMS\Scanner\Domain\Model\FileMatches;
use TYPO3\CMS\Scanner\Domain\Model\Match;
use TYPO3\CMS\Scanner\Matcher\AbstractCoreMatcher;

final class Typo3ScanTask extends AbstractExternalTask
{
    /**
     * @var Paths
     */
    private $paths;

    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'extension_paths' => [],
            'target_version' => null,
            'types_of_changes' => ['breaking','deprecation','feature','important'],
            'indicators' => ['strong','weak'],
            'triggered_by' => ['php']
        ]);

        $resolver->setAllowedValues('target_version', function ($value) {
            if ($value === null) {
                return true;
            }
            return false !== filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        });

        $resolver->addAllowedTypes('extension_paths', ['array']);
        $resolver->addAllowedTypes('types_of_changes', ['array']);
        $resolver->addAllowedTypes('indicators', ['array']);
        $resolver->addAllowedTypes('triggered_by', ['array']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof RunContext || $context instanceof GitPreCommitContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();

        if (empty($config['extension_paths'])) {
            return TaskResult::createNonBlockingFailed(
                $this,
                $context,
                'You have to give at least one path in extension_paths!'
            );
        }

        $files = $context->getFiles()->paths($config['extension_paths'])->extensions($config['triggered_by']);

        if (0 === count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $this->paths = new Paths();

        $scannerService = new ScannerService($config['target_version'], $this->paths);
        $scannerResult = $scannerService->scan($files);


        $scannerResult = $this->filterIgnoredMatches($scannerResult);
        $scannerResult = $this->filterByType($scannerResult, $config);
        $scannerResult = $this->filterByIndicators($scannerResult, $config);

        $total = $scannerResult->countAll();

        if ($total > 0) {
            $percentagesByType = $this->getPercentagesByType($this->getCountsByType($scannerResult), $total);

            $viewArray = [
                'scannedPaths' => $config['extension_paths'],
                'targetVersion' => $config['target_version'],
                'total' => $scannerResult->countAll(),
                'basePath' => 'basePath',
                'statistics' => $percentagesByType,
                'directoryMatches' => $scannerResult,
            ];

            $loader = new FilesystemLoader([dirname(dirname(__DIR__)) . '/resources/templates']);
            $twig = new Environment($loader);
            $twig->addFilter($this->getChangeTitle());
            $twig->addFilter($this->getEscapeDollarFilter());
            $twig->addFilter($this->getLineFromFileFilter());
            $twig->addFilter($this->getLinesFromFileFilter());
            $twig->addFilter($this->getFilenameFilter());
            $twig->addFilter($this->getOnlineDocumentFilter());
            $template = $twig->load('Plain.twig');
            $message = $template->render($viewArray);
            return TaskResult::createFailed($this, $context, $message);
        }

        return TaskResult::createPassed($this, $context);
    }

    /**
     * @param
     * @param
     * @return
     */
    protected function filterByType(DirectoryMatches $directoryMatches, array $config): DirectoryMatches
    {
        $only = $config['types_of_changes'];
        $only = array_map('strtoupper', $only);

        $path = $directoryMatches->getPath();

        $filteredDirectoryMatches = new DirectoryMatches($path);

        /**
         * @var
         */
        foreach ($directoryMatches as $fileMatches) {
            $filteredFileMatches = new FileMatches($fileMatches->getPath());
            /**
             * @var
             */
            foreach ($fileMatches as $fileMatch) {
                if (in_array($fileMatch->getType(), $only, true)) {
                    $filteredFileMatches->append($fileMatch);
                }
            }
            if (count($filteredFileMatches)) {
                $filteredDirectoryMatches->append($filteredFileMatches);
            }
        }

        return $filteredDirectoryMatches;
    }

    /**
     * @param
     * @param
     * @return
     */
    protected function filterByIndicators(DirectoryMatches $directoryMatches, array $config): DirectoryMatches
    {
        $indicators = $config['indicators'];
        $indicators = array_map('strtoupper', $indicators);

        $path = $directoryMatches->getPath();

        $filteredDirectoryMatches = new DirectoryMatches($path);

        /**
         * @var
         */
        foreach ($directoryMatches as $fileMatches) {
            $filteredFileMatches = new FileMatches($fileMatches->getPath());
            /**
             * @var
             */
            foreach ($fileMatches as $fileMatch) {
                if (in_array(strtoupper($fileMatch->getIndicator()), $indicators, true)) {
                    $filteredFileMatches->append($fileMatch);
                }
            }
            if (count($filteredFileMatches)) {
                $filteredDirectoryMatches->append($filteredFileMatches);
            }
        }

        return $filteredDirectoryMatches;
    }

    /**
     * @return
     */
    protected function getLineFromFileFilter(): TwigFilter
    {
        return new TwigFilter('getLineFromFile', function ($fileName, $lineNumber) {
            return $this->getLineFromFile($fileName, $lineNumber);
        });
    }

    /**
     * @return
     */
    protected function getLinesFromFileFilter(): TwigFilter
    {
        return new TwigFilter('getLinesFromFile', function ($fileName, $lineNumber, $before = 2, $after = 2) {
            return $this->getLinesFromFile($fileName, $lineNumber, $before, $after);
        });
    }

    /**
     * @return
     */
    protected function getFilenameFilter(): TwigFilter
    {
        return new TwigFilter('getFilename', function ($path) {
            return pathinfo($path, PATHINFO_FILENAME);
        });
    }

    /**
     * @return
     */
    protected function getEscapeDollarFilter(): TwigFilter
    {
        return new TwigFilter('escapeDollar', function ($string) {
            return str_replace('$', '\$', $string);
        });
    }

    /**
     * @return
     */
    protected function getOnlineDocumentFilter(): TwigFilter
    {
        return new TwigFilter('getOnlineDocument', function ($path) {
            return $this->findOnlineDocumentation($path);
        });
    }

    /**
     * @return
     */
    protected function getChangeTitle(): TwigFilter
    {
        return new TwigFilter('getChangeTitle', function ($path) {
            $rstPath = $this->findRstFile($path);
            return $this->extractTitleFromRstFile($rstPath);
        });
    }

    /**
     * @param
     * @param
     * @return
     */
    protected function getLineFromFile($fileName, $lineNumber): string
    {
        $file = new \SplFileObject($fileName);
        if (!$file->eof()) {
            $file->seek($lineNumber - 1);
            return trim($file->current());
        }
        return '';
    }

    /**
     * @param
     * @param
     * @param
     * @param
     * @return
     */
    protected function getLinesFromFile($fileName, $lineNumber, $before = 2, $after = 2): string
    {
        $before = abs($before);
        $after = abs($after);
        $file = new \SplFileObject($fileName);
        $lines = [];

        $start = $lineNumber - 1 - $before;
        $start = ($start < 0) ? 0 : $start;
        $end = $start + $before + $after + 1;

        for ($position = $start; $position < $end; $position++) {
            if (!$file->eof()) {
                $file->seek($position);
                $lines[] = trim($file->current());
            }
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * @param
     * @return
     */
    protected function findRstFile($path): string
    {
        static $restFiles = [];
        if (empty($restFiles)) {
            $restFinder = new Finder();
            $restFilesList = $restFinder->files()->in(dirname(dirname(__DIR__)) . '/resources/Changelog')->name('*.rst');
            /**
             * @var
             */
            foreach ($restFilesList as $restFile) {
                $restFiles[basename($restFile->getPathname())] = $restFile->getPathname();
            }

            $restFiles['Deprecation-legacy-files.md'] = 'Deprecation-legacy-files.md';
            $restFiles['Deprecation-non-namespaced.md'] = 'Deprecation-non-namespaced.md';
        }
        return $restFiles[basename($path)];
    }

    /**
     * @param
     * @return
     */
    protected function extractTitleFromRstFile($path): string
    {
        static $restFileTitles = [

            3899088142 => 'Renamed TYPO3 core libraries',
            3619663099 => 'Use of non-namespaced classes'
        ];
        $cacheKey = crc32($path);
        if (array_key_exists($cacheKey, $restFileTitles)) {
            return $restFileTitles[$cacheKey];
        }
        $result = '';
        $thisShouldBeTheHeader = false;
        $fileHandle = fopen($path, 'r');

        while (($line = fgets($fileHandle)) !== false) {
            if ($thisShouldBeTheHeader) {
                $result = trim($line);
                break;
            }
            if (strpos($line, '============') === 0) {
                $thisShouldBeTheHeader = true;
            }
        }
        $restFileTitles[$cacheKey] = $result;
        return $result;
    }

    /**
     * @param
     * @return
     */
    protected function findOnlineDocumentation($path): string
    {
        static $onlineDocumentationLinks = [

            1299497039 => 'https://gist.github.com/Tuurlijk/f857bf41e559ce3908290fb96d98b5e4',
            2959317077 => 'https://gist.github.com/Tuurlijk/79aba880880e6340ffd2720ff1c5b623'
        ];
        $cacheKey = crc32($path);
        if (array_key_exists($cacheKey, $onlineDocumentationLinks)) {
            return $onlineDocumentationLinks[$cacheKey];
        }
        $onlineDocument = '';
        $base = 'https://docs.typo3.org/typo3cms/extensions/core/';
        $links = file(dirname(dirname(__DIR__)) . '/resources/links.txt');
        $filename = basename($path);
        $filename = str_replace('.rst', '.html', $filename);
        foreach ($links as $link) {
            $link = rtrim($link);
            if (substr($link, -\strlen($filename)) === $filename) {
                $onlineDocument = $base . $link;
                break;
            }
        }
        $onlineDocumentationLinks[$cacheKey] = $onlineDocument;
        return $onlineDocument;
    }

    /**
     * @param
     * @return
     */
    protected function getCountsByType($directoryMatches): array
    {
        $countsByType = [
            AbstractCoreMatcher::INDICATOR_STRONG => 0,
            AbstractCoreMatcher::INDICATOR_WEAK => 0
        ];
        foreach ($directoryMatches as $fileMatches) {
            /**
             * @var
             */
            foreach ($fileMatches as $fileMatch) {
                if (!array_key_exists($fileMatch->getIndicator(), $countsByType)) {
                    $countsByType[$fileMatch->getIndicator()] = 0;
                }
                $countsByType[$fileMatch->getIndicator()]++;
                if (!array_key_exists($fileMatch->getType(), $countsByType)) {
                    $countsByType[$fileMatch->getType()] = 0;
                }
                $countsByType[$fileMatch->getType()]++;
            }
        }
        return $countsByType;
    }

    /**
     * @param
     * @param
     * @return
     */
    protected function getPercentagesByType($counts, $total): array
    {
        $result = [];
        foreach ($counts as $type => $count) {
            if ($total <= 0) {
                $result[$type] = 0;
            } else {
                $result[$type] = number_format(100 * $count / $total, 1) . '% (' . $count . ')';
            }
        }
        return $result;
    }

    private function filterIgnoredMatches(DirectoryMatches $scannerResult): DirectoryMatches
    {
        $ignoreFile = $this->paths->getTypo3ComposerRoot() . '/ignore-typo3scan.json';

        if (!file_exists($ignoreFile)) {
            return $scannerResult;
        }

        $ignoreJson = file_get_contents($ignoreFile);

        $json = json_decode($ignoreJson, true);

        $path = $scannerResult->getPath();

        $filteredScannerResult = new DirectoryMatches($path);

        /** @var FileMatches $fileMatches */
        foreach ($scannerResult as $fileMatches) {
            $filteredFileMatches = new FileMatches($fileMatches->getPath());
            /** @var Match $fileMatch */
            foreach ($fileMatches as $fileMatch) {
                $ignoreMatch = false;
                foreach ($json['ignore'] ?? [] as $ignoreItem) {
                    if ($ignoreItem['file'] === $fileMatches->getPath() && (int)$ignoreItem['line'] === (int)$fileMatch->getLine() && $ignoreItem['message'] === $fileMatch->getMessage()) {
                        $ignoreMatch = true;
                    }
                }
                if (!$ignoreMatch) {
                    $filteredFileMatches->append($fileMatch);
                }
            }
            if (count($filteredFileMatches)) {
                $filteredScannerResult->append($filteredFileMatches);
            }
        }

        return $filteredScannerResult;
    }
}
