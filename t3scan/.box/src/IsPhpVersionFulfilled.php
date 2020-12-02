<?php

namespace _HumbugBox5b688479ab92c\KevinGH\RequirementChecker;

use _HumbugBox5b688479ab92c\Composer\Semver\Semver;
/**
@private
*/
final class IsPhpVersionFulfilled implements \_HumbugBox5b688479ab92c\KevinGH\RequirementChecker\IsFulfilled
{
    private $requiredPhpVersion;
    /**
    @param
    */
    public function __construct($requiredPhpVersion)
    {
        $this->requiredPhpVersion = $requiredPhpVersion;
    }
    public function __invoke()
    {
        return \_HumbugBox5b688479ab92c\Composer\Semver\Semver::satisfies(\sprintf('%d.%d.%d', \PHP_MAJOR_VERSION, \PHP_MINOR_VERSION, \PHP_RELEASE_VERSION), $this->requiredPhpVersion);
    }
}
