<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Stmt;

class Static_ extends Stmt
{
/**
@var */
public $vars;

/**
@param
@param


*/
public function __construct(array $vars, array $attributes = []) {
$this->attributes = $attributes;
$this->vars = $vars;
}

public function getSubNodeNames() : array {
return ['vars'];
}

public function getType() : string {
return 'Stmt_Static';
}
}
