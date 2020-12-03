<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Eval_ extends Expr
{
/**
@var */
public $expr;

/**
@param
@param


*/
public function __construct(Expr $expr, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
}

public function getSubNodeNames() : array {
return ['expr'];
}

public function getType() : string {
return 'Expr_Eval';
}
}
