<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Service;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Task\Composer;
use PhpParser\ParserFactory;
use Pluswerk\GrumPHPTypo3ScanTask\Paths;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scanner\Domain\Model\DirectoryMatches;
use TYPO3\CMS\Scanner\Domain\Model\MatcherBundle;
use TYPO3\CMS\Scanner\Domain\Model\MatcherBundleCollection;
use TYPO3\CMS\Scanner\Matcher;
use TYPO3\CMS\Scanner\ScannerFactory;

final class ScannerService
{
    /**
     * @var MatcherBundleCollection
     */
    private $matcherBundleCollection;

    /**
     * @var
     */
    private $scanner;

    /**
     * @var Paths
     */
    private $paths;

    public function __construct(string $version, Paths $paths)
    {
        $this->paths = $paths;

        switch ($version) {
            case '10':
                $this->matcherBundleCollection = new MatcherBundleCollection(
                    new MatcherBundle(
                        $this->paths->getConfigurationPathByVersion($version),
                        $this->paths->getTypo3Changelog(),
                        Matcher\ArrayDimensionMatcher::class,
                        Matcher\ArrayGlobalMatcher::class,
                        Matcher\ClassConstantMatcher::class,
                        Matcher\ClassNameMatcher::class,
                        Matcher\ConstantMatcher::class,
                        Matcher\ConstructorArgumentMatcher::class,
                        Matcher\FunctionCallMatcher::class,
                        Matcher\InterfaceMethodChangedMatcher::class,
                        Matcher\MethodAnnotationMatcher::class,
                        Matcher\MethodArgumentDroppedMatcher::class,
                        Matcher\MethodArgumentDroppedStaticMatcher::class,
                        Matcher\MethodArgumentRequiredMatcher::class,
                        Matcher\MethodArgumentRequiredStaticMatcher::class,
                        Matcher\MethodArgumentUnusedMatcher::class,
                        Matcher\MethodCallMatcher::class,
                        Matcher\MethodCallStaticMatcher::class,
                        Matcher\PropertyAnnotationMatcher::class,
                        Matcher\PropertyExistsStaticMatcher::class,
                        Matcher\PropertyProtectedMatcher::class,
                        Matcher\PropertyPublicMatcher::class
                    )
                );
                break;
            case '9':
                $this->matcherBundleCollection = new MatcherBundleCollection(
                    new MatcherBundle(
                        $this->paths->getConfigurationPathByVersion($version),
                        $this->paths->getTypo3Changelog(),
                        Matcher\ArrayDimensionMatcher::class,
                        Matcher\ArrayGlobalMatcher::class,
                        Matcher\ClassConstantMatcher::class,
                        Matcher\ClassNameMatcher::class,
                        Matcher\ConstantMatcher::class,
                        Matcher\FunctionCallMatcher::class,
                        Matcher\InterfaceMethodChangedMatcher::class,
                        Matcher\MethodAnnotationMatcher::class,
                        Matcher\MethodArgumentDroppedMatcher::class,
                        Matcher\MethodArgumentDroppedStaticMatcher::class,
                        Matcher\MethodArgumentRequiredMatcher::class,
                        Matcher\MethodArgumentRequiredStaticMatcher::class,
                        Matcher\MethodArgumentUnusedMatcher::class,
                        Matcher\MethodCallMatcher::class,
                        Matcher\MethodCallStaticMatcher::class,
                        Matcher\PropertyAnnotationMatcher::class,
                        Matcher\PropertyExistsStaticMatcher::class,
                        Matcher\PropertyProtectedMatcher::class,
                        Matcher\PropertyPublicMatcher::class
                    )
                );
                break;
            case '8':
                $this->matcherBundleCollection = new MatcherBundleCollection(
                    new MatcherBundle(
                        $this->paths->getConfigurationPathByVersion($version),
                        $this->paths->getTypo3Changelog(),
                        Matcher\ArrayDimensionMatcher::class,
                        Matcher\ArrayGlobalMatcher::class,
                        Matcher\ClassNameMatcher::class,
                        Matcher\ConstantMatcher::class,
                        Matcher\MethodArgumentDroppedMatcher::class,
                        Matcher\MethodArgumentDroppedStaticMatcher::class,
                        Matcher\MethodArgumentRequiredMatcher::class,
                        Matcher\MethodArgumentUnusedMatcher::class,
                        Matcher\MethodCallMatcher::class,
                        Matcher\MethodCallStaticMatcher::class,
                        Matcher\PropertyPublicMatcher::class
                    )
                );
                break;
            case '7':
            default:
                $this->matcherBundleCollection = new MatcherBundleCollection(
                    new MatcherBundle(
                        $this->paths->getConfigurationPathByVersion($version),
                        $this->paths->getTypo3Changelog(),
                        Matcher\ArrayMatcher::class,
                        Matcher\ArrayDimensionMatcher::class,
                        Matcher\ArrayGlobalMatcher::class,
                        Matcher\ClassConstantMatcher::class,
                        Matcher\ClassNameMatcher::class,
                        Matcher\ClassNamePatternMatcher::class,
                        Matcher\ConstantMatcher::class,
                        Matcher\GlobalMatcher::class,
                        Matcher\MethodArgumentDroppedMatcher::class,
                        Matcher\MethodArgumentRequiredMatcher::class,
                        Matcher\MethodArgumentUnusedStaticMatcher::class,
                        Matcher\MethodCallMatcher::class,
                        Matcher\MethodCallStaticMatcher::class,
                        Matcher\PropertyProtectedMatcher::class,
                        Matcher\PropertyPublicMatcher::class
                    )
                );
                break;
        }

        $this->scanner = ScannerFactory::create()->createFor(ParserFactory::PREFER_PHP7);
    }

    public function scan(FilesCollection $files)
    {
        $directoryMatches = new DirectoryMatches($this->paths->getTypo3ComposerRoot());

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $fileMatches = $this->scanner->scanFile($file->getPathname(), $this->matcherBundleCollection);

            if ($fileMatches->count() !== 0) {
                $directoryMatches[] = $fileMatches;
            }
        }

        return $directoryMatches;
    }
}
