<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class Attribute extends NodeAbstract
{
/**
@var */
public $name;

/**
@var */
public $args;

/**
@param
@param
@param
*/
public function __construct(Name $name, array $args = [], array $attributes = []) {
$this->attributes = $attributes;
$this->name = $name;
$this->args = $args;
}

public function getSubNodeNames() : array {
return ['name', 'args'];
}

public function getType() : string {
return 'Attribute';
}
}
