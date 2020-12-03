<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class ArrayDimFetch extends Expr
{
/**
@var */
public $var;
/**
@var */
public $dim;

/**
@param
@param
@param


*/
public function __construct(Expr $var, Expr $dim = null, array $attributes = []) {
$this->attributes = $attributes;
$this->var = $var;
$this->dim = $dim;
}

public function getSubNodeNames() : array {
return ['var', 'dim'];
}

public function getType() : string {
return 'Expr_ArrayDimFetch';
}
}
