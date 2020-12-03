<?php

namespace MichielRoos\TYPO3Scan\Command;


use MichielRoos\TYPO3Scan\Service\ScannerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Scanner\Domain\Model\DirectoryMatches;
use TYPO3\CMS\Scanner\Domain\Model\FileMatches;
use TYPO3\CMS\Scanner\Domain\Model\Match;
use TYPO3\CMS\Scanner\Matcher\AbstractCoreMatcher;

/**
 * @package
 */
class ScanCommand extends Command
{


    protected function configure()
    {
        $this
            ->setName('scan')
            ->setDescription('Scan a path for deprecated code')
            ->setDefinition([
                new InputArgument('path', InputArgument::REQUIRED, 'Path to scan'),
                new InputOption('target', 't', InputOption::VALUE_OPTIONAL, 'TYPO3 version to target', '10'),
                new InputOption('only', 'o', InputOption::VALUE_OPTIONAL, 'Only report: [breaking, deprecation, important, feature] changes', 'breaking,deprecation,important,feature'),
                new InputOption('indicators', 'i', InputOption::VALUE_OPTIONAL, 'Only report: [strong, weak] matches', 'strong,weak'),
                new InputOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format', 'plain'),
                new InputOption('reportFile', 'r', InputOption::VALUE_OPTIONAL, 'Report file', null),
                new InputOption('templatePath', null, InputOption::VALUE_OPTIONAL, 'Path to template folder'),
            ])
            ->setHelp(<<<EOT
The <info>scan</info> command scans a path for deprecated code</info>.

Scan a folder:
<info>php typo3scan.phar scan ~/tmp/source</info>

Scan a folder for v8 changes:
<info>php typo3scan.phar scan --target 8 ~/tmp/source</info>

Scan a folder and output to report file:
<info>php typo3scan.phar scan --target 8 --reportFile ~/tmp/report.txt ~/tmp/source</info>

Scan a folder for v7 changes and output in markdown:
<info>php typo3scan.phar scan --target 7 --format markdown ~/tmp/source</info>

Scan a folder for v7 WEAK changes and output in markdown:
<info>php typo3scan.phar scan --indicator weak --target 7 --format markdown ~/tmp/source</info>

Scan a folder for v9 changes and output in markdown with custom template:
<info>php typo3scan.phar scan --format markdown --templatePath ~/path/to/templates --path ~/tmp/source</info>

Scan a folder for v7 changes, only show the breaking changes and output in markdown:
<info>php typo3scan.phar scan --target 7 --only breaking --format markdown ~/tmp/source</info>
EOT
            );
    }

    /**
     * @param
     * @param
     * @return
     * @throws
     * @throws
     * @throws
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stdErr = $output;
        if ($output instanceof ConsoleOutputInterface) {
            $stdErr = $output->getErrorOutput();
        }

        $startTime = microtime(true);
        $format = $input->getOption('format') ?: 'plain';
        $path = realpath($input->getArgument('path'));
        if (!is_dir($path)) {
            $stdErr->writeln(sprintf('Path does not exist: "%s"', $input->getArgument('path')));
            exit;
        }


        $version = $input->getOption('target');


        if ($input->getOption('templatePath') && is_dir(realpath($input->getOption('templatePath')))) {
            $templatePaths[] = realpath($input->getOption('templatePath'));
        }
        $templatePaths[] = __DIR__ . '/../../Resources/Private/Templates';

        $basePath = $path;
        $extension = '';

        if ($this->pathContainsExt($path)) {
            $extension = $this->getExtKeyFromPath($path);
            $basePath = $this->getExtPath($path) . DIRECTORY_SEPARATOR . $extension . DIRECTORY_SEPARATOR;
        }

        $scanner = new ScannerService($version);
        $directoryMatches = $scanner->scan($path);

        $directoryMatches = $this->filterByType($directoryMatches, $input);
        $directoryMatches = $this->filterByIndicators($directoryMatches, $input);

        $total = $directoryMatches->countAll();

        $executionTime = microtime(true) - $startTime;

        $percentagesByType = $this->getPercentagesByType($this->getCountsByType($directoryMatches), $total);

        $loader = new \Twig_Loader_Filesystem($templatePaths);
        $twig = new \Twig_Environment($loader);
        $twig->addFilter($this->getChangeTitle());
        $twig->addFilter($this->getEscapeDollarFilter());
        $twig->addFilter($this->getLineFromFileFilter());
        $twig->addFilter($this->getLinesFromFileFilter());
        $twig->addFilter($this->getFilenameFilter());
        $twig->addFilter($this->getOnlineDocumentFilter());

        $context = [
            'title' => $extension ?: $path,
            'targetVersion' => $version,
            'total' => $total,
            'basePath' => $basePath,
            'statistics' => $percentagesByType,
            'directoryMatches' => $directoryMatches,
            'executionTime' => $executionTime
        ];

        $template = $twig->load(ucfirst($format) . '.twig');


        if ($input->getOption('reportFile')) {
            $pathInfo = pathinfo($input->getOption('reportFile'));

            if (!is_dir($pathInfo['dirname'])) {
                $stdErr->writeln(sprintf('Reportfile path does not exist: "%s"', $pathInfo['dirname']));
                exit;
            }
            $reportFile = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'];
            $filesystem = new Filesystem();
            try {
                $filesystem->touch($reportFile);
            } catch (IOExceptionInterface $exception) {
                echo 'An error occurred while creating your report at ' . $exception->getPath();
            }
            $filesystem->dumpFile($reportFile, $template->render($context));
        } else {
            $output->write($template->render($context));
        }

        if ($total > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * @param
     * @param
     * @return
     */
    protected function filterByType(DirectoryMatches $directoryMatches, InputInterface $input): DirectoryMatches
    {
        $only = explode(',', $input->getOption('only'));
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
    protected function filterByIndicators(DirectoryMatches $directoryMatches, InputInterface $input): DirectoryMatches
    {
        $indicators = explode(',', $input->getOption('indicators'));
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
    protected function getLineFromFileFilter(): \Twig_Filter
    {
        return new \Twig_Filter('getLineFromFile', function ($fileName, $lineNumber) {
            return $this->getLineFromFile($fileName, $lineNumber);
        });
    }

    /**
     * @return
     */
    protected function getLinesFromFileFilter(): \Twig_Filter
    {
        return new \Twig_Filter('getLinesFromFile', function ($fileName, $lineNumber, $before = 2, $after = 2) {
            return $this->getLinesFromFile($fileName, $lineNumber, $before, $after);
        });
    }

    /**
     * @return
     */
    protected function getFilenameFilter(): \Twig_Filter
    {
        return new \Twig_Filter('getFilename', function ($path) {
            return pathinfo($path, PATHINFO_FILENAME);
        });
    }

    /**
     * @return
     */
    protected function getEscapeDollarFilter(): \Twig_Filter
    {
        return new \Twig_Filter('escapeDollar', function ($string) {
            return str_replace('$', '\$', $string);
        });
    }

    /**
     * @return
     */
    protected function getOnlineDocumentFilter(): \Twig_Filter
    {
        return new \Twig_Filter('getOnlineDocument', function ($path) {
            return $this->findOnlineDocumentation($path);
        });
    }

    /**
     * @return
     */
    protected function getChangeTitle(): \Twig_Filter
    {
        return new \Twig_Filter('getChangeTitle', function ($path) {
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
    protected function pathContainsExt($path): bool
    {
        while ($dir = basename($path)) {
            if ($dir === 'ext') {
                return true;
            }
            $newPath = \dirname($path);
            if ($newPath === $path) {
                break;
            }
            $path = $newPath;
        }
        return false;
    }

    /**
     * @param
     * @return
     */
    protected function getExtPath($path): string
    {
        while ($dir = basename($path)) {
            if ($dir === 'ext') {
                return $path;
            }
            $path = \dirname($path);
        }
        return $path;
    }

    /**
     * @param
     * @return
     */
    protected function getExtKeyFromPath($path): string
    {
        $extensionName = '';
        while ($dir = basename($path)) {
            if ($dir === 'ext') {
                return $extensionName;
            }
            $extensionName = $dir;
            $path = \dirname($path);
        }
        return $extensionName;
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

    /**
     * @param
     * @return
     */
    protected function findRstFile($path): string
    {
        static $restFiles = [];
        if (empty($restFiles)) {
            $restFinder = new Finder();
            $restFilesList = $restFinder->files()->in(__DIR__ . '/../../Resources/Private/Changelog')->name('*.rst');
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
        $links = file(__DIR__ . '/../../Resources/Private/links.txt');
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
}
