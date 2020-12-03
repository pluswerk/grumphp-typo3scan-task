<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class ConstFetch extends Expr
{
/**
@var */
public $name;

/**
@param
@param


*/
public function __construct(Name $name, array $attributes = []) {
$this->attributes = $attributes;
$this->name = $name;
}

public function getSubNodeNames() : array {
return ['name'];
}

public function getType() : string {
return 'Expr_ConstFetch';
}
}
