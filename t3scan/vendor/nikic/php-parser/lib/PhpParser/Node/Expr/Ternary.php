<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class Ternary extends Expr
{
/**
@var */
public $cond;
/**
@var */
public $if;
/**
@var */
public $else;

/**
@param
@param
@param
@param


*/
public function __construct(Expr $cond, $if, Expr $else, array $attributes = []) {
$this->attributes = $attributes;
$this->cond = $cond;
$this->if = $if;
$this->else = $else;
}

public function getSubNodeNames() : array {
return ['cond', 'if', 'else'];
}

public function getType() : string {
return 'Expr_Ternary';
}
}
