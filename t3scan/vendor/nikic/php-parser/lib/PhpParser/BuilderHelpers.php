<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;
use PhpParser\Node\UnionType;

/**
@internal


*/
final class BuilderHelpers
{
/**
@param
@return



*/
public static function normalizeNode($node) : Node {
if ($node instanceof Builder) {
return $node->getNode();
} elseif ($node instanceof Node) {
return $node;
}

throw new \LogicException('Expected node or builder object');
}

/**
@param
@return





*/
public static function normalizeStmt($node) : Stmt {
$node = self::normalizeNode($node);
if ($node instanceof Stmt) {
return $node;
}

if ($node instanceof Expr) {
return new Stmt\Expression($node);
}

throw new \LogicException('Expected statement or expression node');
}

/**
@param
@return



*/
public static function normalizeIdentifier($name) : Identifier {
if ($name instanceof Identifier) {
return $name;
}

if (\is_string($name)) {
return new Identifier($name);
}

throw new \LogicException('Expected string or instance of Node\Identifier');
}

/**
@param
@return



*/
public static function normalizeIdentifierOrExpr($name) {
if ($name instanceof Identifier || $name instanceof Expr) {
return $name;
}

if (\is_string($name)) {
return new Identifier($name);
}

throw new \LogicException('Expected string or instance of Node\Identifier or Node\Expr');
}

/**
@param
@return



*/
public static function normalizeName($name) : Name {
return self::normalizeNameCommon($name, false);
}

/**
@param
@return



*/
public static function normalizeNameOrExpr($name) {
return self::normalizeNameCommon($name, true);
}

/**
@param
@param
@return



*/
private static function normalizeNameCommon($name, bool $allowExpr) {
if ($name instanceof Name) {
return $name;
} elseif (is_string($name)) {
if (!$name) {
throw new \LogicException('Name cannot be empty');
}

if ($name[0] === '\\') {
return new Name\FullyQualified(substr($name, 1));
} elseif (0 === strpos($name, 'namespace\\')) {
return new Name\Relative(substr($name, strlen('namespace\\')));
} else {
return new Name($name);
}
}

if ($allowExpr) {
if ($name instanceof Expr) {
return $name;
}
throw new \LogicException(
'Name must be a string or an instance of Node\Name or Node\Expr'
);
} else {
throw new \LogicException('Name must be a string or an instance of Node\Name');
}
}

/**
@param
@return






*/
public static function normalizeType($type) {
if (!is_string($type)) {
if (
!$type instanceof Name && !$type instanceof Identifier &&
!$type instanceof NullableType && !$type instanceof UnionType
) {
throw new \LogicException(
'Type must be a string, or an instance of Name, Identifier, NullableType or UnionType'
);
}
return $type;
}

$nullable = false;
if (strlen($type) > 0 && $type[0] === '?') {
$nullable = true;
$type = substr($type, 1);
}

$builtinTypes = [
'array', 'callable', 'string', 'int', 'float', 'bool', 'iterable', 'void', 'object', 'mixed'
];

$lowerType = strtolower($type);
if (in_array($lowerType, $builtinTypes)) {
$type = new Identifier($lowerType);
} else {
$type = self::normalizeName($type);
}

if ($nullable && (string) $type === 'void') {
throw new \LogicException('void type cannot be nullable');
}

if ($nullable && (string) $type === 'mixed') {
throw new \LogicException('mixed type cannot be nullable');
}

return $nullable ? new NullableType($type) : $type;
}

/**
@param
@return




*/
public static function normalizeValue($value) : Expr {
if ($value instanceof Node\Expr) {
return $value;
} elseif (is_null($value)) {
return new Expr\ConstFetch(
new Name('null')
);
} elseif (is_bool($value)) {
return new Expr\ConstFetch(
new Name($value ? 'true' : 'false')
);
} elseif (is_int($value)) {
return new Scalar\LNumber($value);
} elseif (is_float($value)) {
return new Scalar\DNumber($value);
} elseif (is_string($value)) {
return new Scalar\String_($value);
} elseif (is_array($value)) {
$items = [];
$lastKey = -1;
foreach ($value as $itemKey => $itemValue) {

 if (null !== $lastKey && ++$lastKey === $itemKey) {
$items[] = new Expr\ArrayItem(
self::normalizeValue($itemValue)
);
} else {
$lastKey = null;
$items[] = new Expr\ArrayItem(
self::normalizeValue($itemValue),
self::normalizeValue($itemKey)
);
}
}

return new Expr\Array_($items);
} else {
throw new \LogicException('Invalid value');
}
}

/**
@param
@return



*/
public static function normalizeDocComment($docComment) : Comment\Doc {
if ($docComment instanceof Comment\Doc) {
return $docComment;
} elseif (is_string($docComment)) {
return new Comment\Doc($docComment);
} else {
throw new \LogicException('Doc comment must be a string or an instance of PhpParser\Comment\Doc');
}
}

/**
@param
@param
@return



*/
public static function addModifier(int $modifiers, int $modifier) : int {
Stmt\Class_::verifyModifier($modifiers, $modifier);
return $modifiers | $modifier;
}
}
