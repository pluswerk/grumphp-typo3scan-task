<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;

interface FunctionLike extends Node
{
/**
@return


*/
public function returnsByRef() : bool;

/**
@return


*/
public function getParams() : array;

/**
@return


*/
public function getReturnType();

/**
@return


*/
public function getStmts();

/**
@return


*/
public function getAttrGroups() : array;
}
