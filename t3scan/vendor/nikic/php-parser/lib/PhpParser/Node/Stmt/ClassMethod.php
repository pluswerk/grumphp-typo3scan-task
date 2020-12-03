<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

class ClassMethod extends Node\Stmt implements FunctionLike
{
/**
@var */
public $flags;
/**
@var */
public $byRef;
/**
@var */
public $name;
/**
@var */
public $params;
/**
@var */
public $returnType;
/**
@var */
public $stmts;
/**
@var */
public $attrGroups;

private static $magicNames = [
'__construct' => true,
'__destruct' => true,
'__call' => true,
'__callstatic' => true,
'__get' => true,
'__set' => true,
'__isset' => true,
'__unset' => true,
'__sleep' => true,
'__wakeup' => true,
'__tostring' => true,
'__set_state' => true,
'__clone' => true,
'__invoke' => true,
'__debuginfo' => true,
];

/**
@param
@param
@param








*/
public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->flags = $subNodes['flags'] ?? $subNodes['type'] ?? 0;
$this->byRef = $subNodes['byRef'] ?? false;
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->params = $subNodes['params'] ?? [];
$returnType = $subNodes['returnType'] ?? null;
$this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
$this->stmts = array_key_exists('stmts', $subNodes) ? $subNodes['stmts'] : [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames() : array {
return ['attrGroups', 'flags', 'byRef', 'name', 'params', 'returnType', 'stmts'];
}

public function returnsByRef() : bool {
return $this->byRef;
}

public function getParams() : array {
return $this->params;
}

public function getReturnType() {
return $this->returnType;
}

public function getStmts() {
return $this->stmts;
}

public function getAttrGroups() : array {
return $this->attrGroups;
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
public function isAbstract() : bool {
return (bool) ($this->flags & Class_::MODIFIER_ABSTRACT);
}

/**
@return


*/
public function isFinal() : bool {
return (bool) ($this->flags & Class_::MODIFIER_FINAL);
}

/**
@return


*/
public function isStatic() : bool {
return (bool) ($this->flags & Class_::MODIFIER_STATIC);
}

/**
@return


*/
public function isMagic() : bool {
return isset(self::$magicNames[$this->name->toLowerString()]);
}

public function getType() : string {
return 'Stmt_ClassMethod';
}
}
