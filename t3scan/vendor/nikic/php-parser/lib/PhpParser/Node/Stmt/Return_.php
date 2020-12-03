<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Return_ extends Node\Stmt
{
/**
@var */
public $expr;

/**
@param
@param


*/
public function __construct(Node\Expr $expr = null, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
}

public function getSubNodeNames() : array {
return ['expr'];
}

public function getType() : string {
return 'Stmt_Return';
}
}
