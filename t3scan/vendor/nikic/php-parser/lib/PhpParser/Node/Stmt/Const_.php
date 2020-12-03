<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Const_ extends Node\Stmt
{
/**
@var */
public $consts;

/**
@param
@param


*/
public function __construct(array $consts, array $attributes = []) {
$this->attributes = $attributes;
$this->consts = $consts;
}

public function getSubNodeNames() : array {
return ['consts'];
}

public function getType() : string {
return 'Stmt_Const';
}
}
