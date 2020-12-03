<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;

class MethodCall extends Expr
{
/**
@var */
public $var;
/**
@var */
public $name;
/**
@var */
public $args;

/**
@param
@param
@param
@param


*/
public function __construct(Expr $var, $name, array $args = [], array $attributes = []) {
$this->attributes = $attributes;
$this->var = $var;
$this->name = \is_string($name) ? new Identifier($name) : $name;
$this->args = $args;
}

public function getSubNodeNames() : array {
return ['var', 'name', 'args'];
}

public function getType() : string {
return 'Expr_MethodCall';
}
}
