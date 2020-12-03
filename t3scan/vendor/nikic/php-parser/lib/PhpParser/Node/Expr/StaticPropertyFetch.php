<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\VarLikeIdentifier;

class StaticPropertyFetch extends Expr
{
/**
@var */
public $class;
/**
@var */
public $name;

/**
@param
@param
@param


*/
public function __construct($class, $name, array $attributes = []) {
$this->attributes = $attributes;
$this->class = $class;
$this->name = \is_string($name) ? new VarLikeIdentifier($name) : $name;
}

public function getSubNodeNames() : array {
return ['class', 'name'];
}

public function getType() : string {
return 'Expr_StaticPropertyFetch';
}
}
