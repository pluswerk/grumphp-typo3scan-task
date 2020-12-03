<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;

class Property extends Node\Stmt
{
/**
@var */
public $flags;
/**
@var */
public $props;
/**
@var */
public $type;
/**
@var */
public $attrGroups;

/**
@param
@param
@param
@param
@param


*/
public function __construct(int $flags, array $props, array $attributes = [], $type = null, array $attrGroups = []) {
$this->attributes = $attributes;
$this->flags = $flags;
$this->props = $props;
$this->type = \is_string($type) ? new Identifier($type) : $type;
$this->attrGroups = $attrGroups;
}

public function getSubNodeNames() : array {
return ['attrGroups', 'flags', 'type', 'props'];
}

/**
@return


*/
public function isPublic() : bool {
return ($this->flags & Class_::MODIFIER_PUBLIC) !== 0
|| ($this->flags & Class_::VISIBILITY_MODIFIER_MASK) === 0;
}

/**
@return


*/
public function isProtected() : bool {
return (bool) ($this->flags & Class_::MODIFIER_PROTECTED);
}

/**
@return


*/
public function isPrivate() : bool {
return (bool) ($this->flags & Class_::MODIFIER_PRIVATE);
}

/**
@return


*/
public function isStatic() : bool {
return (bool) ($this->flags & Class_::MODIFIER_STATIC);
}

public function getType() : string {
return 'Stmt_Property';
}
}
