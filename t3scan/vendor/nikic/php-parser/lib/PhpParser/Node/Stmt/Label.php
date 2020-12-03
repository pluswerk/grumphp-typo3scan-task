<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt;

class Label extends Stmt
{
/**
@var */
public $name;

/**
@param
@param


*/
public function __construct($name, array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Identifier($name) : $name;
}

public function getSubNodeNames() : array {
return ['name'];
}

public function getType() : string {
return 'Stmt_Label';
}
}
