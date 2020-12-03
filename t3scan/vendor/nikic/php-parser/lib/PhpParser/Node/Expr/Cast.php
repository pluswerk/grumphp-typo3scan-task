<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

abstract class Cast extends Expr
{
/**
@var */
public $expr;

/**
@param
@param


*/
public function __construct(Expr $expr, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
}

public function getSubNodeNames() : array {
return ['expr'];
}
}
