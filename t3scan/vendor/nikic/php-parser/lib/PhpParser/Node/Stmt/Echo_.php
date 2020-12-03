<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Echo_ extends Node\Stmt
{
/**
@var */
public $exprs;

/**
@param
@param


*/
public function __construct(array $exprs, array $attributes = []) {
$this->attributes = $attributes;
$this->exprs = $exprs;
}

public function getSubNodeNames() : array {
return ['exprs'];
}

public function getType() : string {
return 'Stmt_Echo';
}
}
