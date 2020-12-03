<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\Identifier;

class UseUse extends Node\Stmt
{
/**
@var */
public $type;
/**
@var */
public $name;
/**
@var */
public $alias;

/**
@param
@param
@param
@param


*/
public function __construct(Node\Name $name, $alias = null, int $type = Use_::TYPE_UNKNOWN, array $attributes = []) {
$this->attributes = $attributes;
$this->type = $type;
$this->name = $name;
$this->alias = \is_string($alias) ? new Identifier($alias) : $alias;
}

public function getSubNodeNames() : array {
return ['type', 'name', 'alias'];
}

/**
@return


*/
public function getAlias() : Identifier {
if (null !== $this->alias) {
return $this->alias;
}

return new Identifier($this->name->getLast());
}

public function getType() : string {
return 'Stmt_UseUse';
}
}
