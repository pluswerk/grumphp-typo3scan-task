<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

abstract class AssignOp extends Expr
{
/**
@var */
public $var;
/**
@var */
public $expr;

/**
@param
@param
@param


*/
public function __construct(Expr $var, Expr $expr, array $attributes = []) {
$this->attributes = $attributes;
$this->var = $var;
$this->expr = $expr;
}

public function getSubNodeNames() : array {
return ['var', 'expr'];
}
}
