<?php

namespace MichielRoos\TYPO3Scan\Service;


use TYPO3\CMS\Scanner\Domain\Model\MatcherBundleCollection;
use TYPO3\CMS\Scanner\Matcher;
use TYPO3\CMS\Scanner\ScannerFactory;

/**
 * @package
 */
class ScannerService
{
    /**
     * @var
     */
    private static $matcherBundleBasePath = '';

    /**
     * @var
     */
    private $collection;

    /**
     * @var
     */
    private $scanner;


    public function __construct($version)
    {
        $this->setMatcherBundlePath();

        switch ($version) {
            case '10':
                $this->collection = new MatcherBundleCollection(
                    new \TYPO3\CMS\Scanner\Domain\Model\MatcherBundle(
                        self::$matcherBundleBasePath . 'v' . $version,
                        '',

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
                $this->collection = new MatcherBundleCollection(
                    new \TYPO3\CMS\Scanner\Domain\Model\MatcherBundle(
                        self::$matcherBundleBasePath . 'v' . $version,
                        '',

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
                $this->collection = new MatcherBundleCollection(
                    new \TYPO3\CMS\Scanner\Domain\Model\MatcherBundle(
                        self::$matcherBundleBasePath . 'v' . $version,
                        '',

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
                $this->collection = new MatcherBundleCollection(
                    new \TYPO3\CMS\Scanner\Domain\Model\MatcherBundle(
                        self::$matcherBundleBasePath . 'v' . $version,
                        '',

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

        $this->scanner = ScannerFactory::create()->createFor(\PhpParser\ParserFactory::PREFER_PHP7);
    }

    public function scan($path)
    {
        $result = $this->scanner->scanPath(
            $path,
            $this->collection
        );
        return $result;
    }


    private function setMatcherBundlePath()
    {
        foreach ([__DIR__ . '/../../../vendor/typo3/cms-scanner/config/Matcher/', __DIR__ . '/../../../../../typo3/cms-scanner/config/Matcher/'] as $file) {
            if (is_dir($file)) {
                $this::$matcherBundleBasePath = $file;
                break;
            }
        }
    }
}
