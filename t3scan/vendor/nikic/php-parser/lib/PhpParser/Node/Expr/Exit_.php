<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Exit_ extends Expr
{

const KIND_EXIT = 1;
const KIND_DIE = 2;

/**
@var */
public $expr;

/**
@param
@param


*/
public function __construct(Expr $expr = null, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
}

public function getSubNodeNames() : array {
return ['expr'];
}

public function getType() : string {
return 'Expr_Exit';
}
}
