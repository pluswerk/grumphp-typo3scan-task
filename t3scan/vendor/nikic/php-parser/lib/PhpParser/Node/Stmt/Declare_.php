<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Declare_ extends Node\Stmt
{
/**
@var */
public $declares;
/**
@var */
public $stmts;

/**
@param
@param
@param


*/
public function __construct(array $declares, array $stmts = null, array $attributes = []) {
$this->attributes = $attributes;
$this->declares = $declares;
$this->stmts = $stmts;
}

public function getSubNodeNames() : array {
return ['declares', 'stmts'];
}

public function getType() : string {
return 'Stmt_Declare';
}
}
