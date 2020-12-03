<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class TryCatch extends Node\Stmt
{
/**
@var */
public $stmts;
/**
@var */
public $catches;
/**
@var */
public $finally;

/**
@param
@param
@param
@param


*/
public function __construct(array $stmts, array $catches, Finally_ $finally = null, array $attributes = []) {
$this->attributes = $attributes;
$this->stmts = $stmts;
$this->catches = $catches;
$this->finally = $finally;
}

public function getSubNodeNames() : array {
return ['stmts', 'catches', 'finally'];
}

public function getType() : string {
return 'Stmt_TryCatch';
}
}
