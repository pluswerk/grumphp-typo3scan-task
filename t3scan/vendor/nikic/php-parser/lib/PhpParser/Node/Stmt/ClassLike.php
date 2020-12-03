<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

/**
@property
*/
abstract class ClassLike extends Node\Stmt
{
/**
@var */
public $name;
/**
@var */
public $stmts;
/**
@var */
public $attrGroups;

/**
@return
*/
public function getTraitUses() : array {
$traitUses = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof TraitUse) {
$traitUses[] = $stmt;
}
}
return $traitUses;
}

/**
@return
*/
public function getConstants() : array {
$constants = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof ClassConst) {
$constants[] = $stmt;
}
}
return $constants;
}

/**
@return
*/
public function getProperties() : array {
$properties = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof Property) {
$properties[] = $stmt;
}
}
return $properties;
}

/**
@param
@return



*/
public function getProperty(string $name) {
foreach ($this->stmts as $stmt) {
if ($stmt instanceof Property) {
foreach ($stmt->props as $prop) {
if ($prop instanceof PropertyProperty && $name === $prop->name->toString()) {
return $stmt;
}
}
}
}
return null;
}

/**
@return


*/
public function getMethods() : array {
$methods = [];
foreach ($this->stmts as $stmt) {
if ($stmt instanceof ClassMethod) {
$methods[] = $stmt;
}
}
return $methods;
}

/**
@param
@return



*/
public function getMethod(string $name) {
$lowerName = strtolower($name);
foreach ($this->stmts as $stmt) {
if ($stmt instanceof ClassMethod && $lowerName === $stmt->name->toLowerString()) {
return $stmt;
}
}
return null;
}
}
