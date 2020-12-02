<?php declare(strict_types=1);

namespace PhpParser;

interface Node
{
/**
@return


*/
public function getType() : string;

/**
@return


*/
public function getSubNodeNames() : array;

/**
@return


*/
public function getLine() : int;

/**
@return




*/
public function getStartLine() : int;

/**
@return




*/
public function getEndLine() : int;

/**
@return






*/
public function getStartTokenPos() : int;

/**
@return






*/
public function getEndTokenPos() : int;

/**
@return




*/
public function getStartFilePos() : int;

/**
@return




*/
public function getEndFilePos() : int;

/**
@return




*/
public function getComments() : array;

/**
@return


*/
public function getDocComment();

/**
@param




*/
public function setDocComment(Comment\Doc $docComment);

/**
@param
@param


*/
public function setAttribute(string $key, $value);

/**
@param
@return



*/
public function hasAttribute(string $key) : bool;

/**
@param
@param
@return



*/
public function getAttribute(string $key, $default = null);

/**
@return


*/
public function getAttributes() : array;

/**
@param


*/
public function setAttributes(array $attributes);
}
