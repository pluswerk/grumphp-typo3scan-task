<?php

namespace _HumbugBox5b688479ab92c\Composer\Semver\Constraint;

interface ConstraintInterface
{
    /**
    @param
    @return
    */
    public function matches(\_HumbugBox5b688479ab92c\Composer\Semver\Constraint\ConstraintInterface $provider);
    /**
    @return
    */
    public function getPrettyString();
    /**
    @return
    */
    public function __toString();
}
