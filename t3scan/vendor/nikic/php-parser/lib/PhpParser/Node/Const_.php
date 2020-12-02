<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
@property
*/
class Const_ extends NodeAbstract
{
/**
@var */
public $name;
/**
@var */
public $value;

/**
@param
@param
@param


*/
public function __construct($name, Expr $value, array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Identifier($name) : $name;
$this->value = $value;
}

public function getSubNodeNames() : array {
return ['name', 'value'];
}

public function getType() : string {
return 'Const';
}
}
