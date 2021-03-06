<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class PostDec extends Expr
{
/**
@var */
public $var;

/**
@param
@param


*/
public function __construct(Expr $var, array $attributes = []) {
$this->attributes = $attributes;
$this->var = $var;
}

public function getSubNodeNames() : array {
return ['var'];
}

public function getType() : string {
return 'Expr_PostDec';
}
}
