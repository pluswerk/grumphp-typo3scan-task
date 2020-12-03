<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Trait_ extends ClassLike
{
/**
@param
@param
@param




*/
public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames() : array {
return ['attrGroups', 'name', 'stmts'];
}

public function getType() : string {
return 'Stmt_Trait';
}
}
