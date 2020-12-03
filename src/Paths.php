<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask;

use TYPO3\CMS\Core\Core\Bootstrap;

final class Paths
{
    /**
     * @var string
     */
    private $typo3Changelog;
    /**
     * @var string
     */
    private $typo3CmsMatcherConfig;
    /**
     * @var string
     */
    private $typo3ComposerRoot;

    public function __construct()
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $this->typo3CmsMatcherConfig = dirname(dirname($reflection->getFileName())) . '/typo3/cms-scanner/config/Matcher';

        $typo3Reflection = new \ReflectionClass(Bootstrap::class);
        $this->typo3Changelog = dirname(dirname($typo3Reflection->getFileName())) . '/Documentation/Changelog';

        $this->typo3ComposerRoot = (string)($_SERVER['TYPO3_PATH_COMPOSER_ROOT'] ?? '');
    }

    /**
     * @param string $version
     * @return string
     */
    public function getConfigurationPathByVersion(string $version)
    {
        return $this->typo3CmsMatcherConfig . '/v' . $version;
    }

    /**
     * @return string
     */
    public function getTypo3Changelog()
    {
        return $this->typo3Changelog;
    }

    /**
     * @return string
     */
    public function getTypo3ComposerRoot()
    {
        return $this->typo3ComposerRoot;
    }
}
