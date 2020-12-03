<?php declare(strict_types=1);

namespace PhpParser;

interface NodeTraverserInterface
{
/**
@param


*/
public function addVisitor(NodeVisitor $visitor);

/**
@param


*/
public function removeVisitor(NodeVisitor $visitor);

/**
@param
@return



*/
public function traverse(array $nodes) : array;
}
