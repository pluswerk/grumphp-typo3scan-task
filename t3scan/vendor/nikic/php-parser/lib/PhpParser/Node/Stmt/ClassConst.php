<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class ClassConst extends Node\Stmt
{
/**
@var */
public $flags;
/**
@var */
public $consts;
/**
@var */
public $attrGroups;

/**
@param
@param
@param
@param


*/
public function __construct(
array $consts,
int $flags = 0,
array $attributes = [],
array $attrGroups = []
) {
$this->attributes = $attributes;
$this->flags = $flags;
$this->consts = $consts;
$this->attrGroups = $attrGroups;
}

public function getSubNodeNames() : array {
return ['attrGroups', 'flags', 'consts'];
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

public function getType() : string {
return 'Stmt_ClassConst';
}
}
