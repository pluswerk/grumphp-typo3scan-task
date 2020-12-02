<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

abstract class BinaryOp extends Expr
{
/**
@var */
public $left;
/**
@var */
public $right;

/**
@param
@param
@param


*/
public function __construct(Expr $left, Expr $right, array $attributes = []) {
$this->attributes = $attributes;
$this->left = $left;
$this->right = $right;
}

public function getSubNodeNames() : array {
return ['left', 'right'];
}

/**
@return





*/
abstract public function getOperatorSigil() : string;
}
