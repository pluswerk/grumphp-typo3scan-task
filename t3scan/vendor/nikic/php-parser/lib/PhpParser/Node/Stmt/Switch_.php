<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Switch_ extends Node\Stmt
{
/**
@var */
public $cond;
/**
@var */
public $cases;

/**
@param
@param
@param


*/
public function __construct(Node\Expr $cond, array $cases, array $attributes = []) {
$this->attributes = $attributes;
$this->cond = $cond;
$this->cases = $cases;
}

public function getSubNodeNames() : array {
return ['cond', 'cases'];
}

public function getType() : string {
return 'Stmt_Switch';
}
}
