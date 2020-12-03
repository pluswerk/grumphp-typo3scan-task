<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

abstract class TraitUseAdaptation extends Node\Stmt
{
/**
@var */
public $trait;
/**
@var */
public $method;
}
