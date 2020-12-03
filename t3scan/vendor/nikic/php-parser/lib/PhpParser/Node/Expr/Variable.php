<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Variable extends Expr
{
/**
@var */
public $name;

/**
@param
@param


*/
public function __construct($name, array $attributes = []) {
$this->attributes = $attributes;
$this->name = $name;
}

public function getSubNodeNames() : array {
return ['name'];
}

public function getType() : string {
return 'Expr_Variable';
}
}
