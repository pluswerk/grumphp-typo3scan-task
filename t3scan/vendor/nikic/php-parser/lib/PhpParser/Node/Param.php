<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

class Param extends NodeAbstract
{
/**
@var */
public $type;
/**
@var */
public $byRef;
/**
@var */
public $variadic;
/**
@var */
public $var;
/**
@var */
public $default;
/**
@var */
public $flags;
/**
@var */
public $attrGroups;

/**
@param
@param
@param
@param
@param
@param
@param
@param


*/
public function __construct(
$var, Expr $default = null, $type = null,
bool $byRef = false, bool $variadic = false,
array $attributes = [],
int $flags = 0,
array $attrGroups = []
) {
$this->attributes = $attributes;
$this->type = \is_string($type) ? new Identifier($type) : $type;
$this->byRef = $byRef;
$this->variadic = $variadic;
$this->var = $var;
$this->default = $default;
$this->flags = $flags;
$this->attrGroups = $attrGroups;
}

public function getSubNodeNames() : array {
return ['attrGroups', 'flags', 'type', 'byRef', 'variadic', 'var', 'default'];
}

public function getType() : string {
return 'Param';
}
}
