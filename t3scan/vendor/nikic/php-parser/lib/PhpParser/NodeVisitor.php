<?php declare(strict_types=1);

namespace PhpParser;

interface NodeVisitor
{
/**
@param
@return







*/
public function beforeTraverse(array $nodes);

/**
@param
@return













*/
public function enterNode(Node $node);

/**
@param
@return















*/
public function leaveNode(Node $node);

/**
@param
@return







*/
public function afterTraverse(array $nodes);
}
