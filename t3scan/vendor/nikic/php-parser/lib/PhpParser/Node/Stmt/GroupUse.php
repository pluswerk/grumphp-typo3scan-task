<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

class GroupUse extends Stmt
{
/**
@var */
public $type;
/**
@var */
public $prefix;
/**
@var */
public $uses;

/**
@param
@param
@param
@param


*/
public function __construct(Name $prefix, array $uses, int $type = Use_::TYPE_NORMAL, array $attributes = []) {
$this->attributes = $attributes;
$this->type = $type;
$this->prefix = $prefix;
$this->uses = $uses;
}

public function getSubNodeNames() : array {
return ['type', 'prefix', 'uses'];
}

public function getType() : string {
return 'Stmt_GroupUse';
}
}
