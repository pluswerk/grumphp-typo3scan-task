<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;







class Error extends Expr
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

public function getType() : string {
return 'Expr_Error';
}
}
