<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Encapsed extends Scalar
{
/**
@var */
public $parts;

/**
@param
@param


*/
public function __construct(array $parts, array $attributes = []) {
$this->attributes = $attributes;
$this->parts = $parts;
}

public function getSubNodeNames() : array {
return ['parts'];
}

public function getType() : string {
return 'Scalar_Encapsed';
}
}
