<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class PropertyProperty extends Node\Stmt
{
/**
@var */
public $name;
/**
@var */
public $default;

/**
@param
@param
@param


*/
public function __construct($name, Node\Expr $default = null, array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Node\VarLikeIdentifier($name) : $name;
$this->default = $default;
}

public function getSubNodeNames() : array {
return ['name', 'default'];
}

public function getType() : string {
return 'Stmt_PropertyProperty';
}
}
