<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class DeclareDeclare extends Node\Stmt
{
/**
@var */
public $key;
/**
@var */
public $value;

/**
@param
@param
@param


*/
public function __construct($key, Node\Expr $value, array $attributes = []) {
$this->attributes = $attributes;
$this->key = \is_string($key) ? new Node\Identifier($key) : $key;
$this->value = $value;
}

public function getSubNodeNames() : array {
return ['key', 'value'];
}

public function getType() : string {
return 'Stmt_DeclareDeclare';
}
}
