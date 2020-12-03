<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;

class NullsafePropertyFetch extends Expr
{
/**
@var */
public $var;
/**
@var */
public $name;

/**
@param
@param
@param


*/
public function __construct(Expr $var, $name, array $attributes = []) {
$this->attributes = $attributes;
$this->var = $var;
$this->name = \is_string($name) ? new Identifier($name) : $name;
}

public function getSubNodeNames() : array {
return ['var', 'name'];
}

public function getType() : string {
return 'Expr_NullsafePropertyFetch';
}
}
