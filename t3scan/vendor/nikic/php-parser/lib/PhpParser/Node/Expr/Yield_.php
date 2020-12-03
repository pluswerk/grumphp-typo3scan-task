<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Yield_ extends Expr
{
/**
@var */
public $key;
/**
@var */
public $value;

/**
@param
@param
@param


*/
public function __construct(Expr $value = null, Expr $key = null, array $attributes = []) {
$this->attributes = $attributes;
$this->key = $key;
$this->value = $value;
}

public function getSubNodeNames() : array {
return ['key', 'value'];
}

public function getType() : string {
return 'Expr_Yield';
}
}
