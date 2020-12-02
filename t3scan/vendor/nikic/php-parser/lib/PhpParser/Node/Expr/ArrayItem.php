<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class ArrayItem extends Expr
{
/**
@var */
public $key;
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


*/
public function __construct(Expr $value, Expr $key = null, bool $byRef = false, array $attributes = [], bool $unpack = false) {
$this->attributes = $attributes;
$this->key = $key;
$this->value = $value;
$this->byRef = $byRef;
$this->unpack = $unpack;
}

public function getSubNodeNames() : array {
return ['key', 'value', 'byRef', 'unpack'];
}

public function getType() : string {
return 'Expr_ArrayItem';
}
}
