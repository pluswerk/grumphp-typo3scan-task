<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Scalar;

abstract class MagicConst extends Scalar
{
/**
@param


*/
public function __construct(array $attributes = []) {
$this->attributes = $attributes;
}

public function getSubNodeNames() : array {
return [];
}

/**
@return


*/
abstract public function getName() : string;
}
