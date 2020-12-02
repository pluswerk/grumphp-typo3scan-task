<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Arg extends NodeAbstract
{
/**
@var */
public $name;
/**
@var */
public $value;
/**
@var */
public $byRef;
/**
@var */
public $unpack;

/**
@param
@param
@param
@param
@param


*/
public function __construct(
Expr $value, bool $byRef = false, bool $unpack = false, array $attributes = [],
Identifier $name = null
) {
$this->attributes = $attributes;
$this->name = $name;
$this->value = $value;
$this->byRef = $byRef;
$this->unpack = $unpack;
}

public function getSubNodeNames() : array {
return ['name', 'value', 'byRef', 'unpack'];
}

public function getType() : string {
return 'Arg';
}
}
