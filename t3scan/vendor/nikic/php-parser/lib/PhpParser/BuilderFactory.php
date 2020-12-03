<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Use_;

class BuilderFactory
{
/**
@param
@return



*/
public function namespace($name) : Builder\Namespace_ {
return new Builder\Namespace_($name);
}

/**
@param
@return



*/
public function class(string $name) : Builder\Class_ {
return new Builder\Class_($name);
}

/**
@param
@return



*/
public function interface(string $name) : Builder\Interface_ {
return new Builder\Interface_($name);
}

/**
@param
@return



*/
public function trait(string $name) : Builder\Trait_ {
return new Builder\Trait_($name);
}

/**
@param
@return



*/
public function useTrait(...$traits) : Builder\TraitUse {
return new Builder\TraitUse(...$traits);
}

/**
@param
@param
@return



*/
public function traitUseAdaptation($trait, $method = null) : Builder\TraitUseAdaptation {
if ($method === null) {
$method = $trait;
$trait = null;
}

return new Builder\TraitUseAdaptation($trait, $method);
}

/**
@param
@return



*/
public function method(string $name) : Builder\Method {
return new Builder\Method($name);
}

/**
@param
@return



*/
public function param(string $name) : Builder\Param {
return new Builder\Param($name);
}

/**
@param
@return



*/
public function property(string $name) : Builder\Property {
return new Builder\Property($name);
}

/**
@param
@return



*/
public function function(string $name) : Builder\Function_ {
return new Builder\Function_($name);
}

/**
@param
@return



*/
public function use($name) : Builder\Use_ {
return new Builder\Use_($name, Use_::TYPE_NORMAL);
}

/**
@param
@return



*/
public function useFunction($name) : Builder\Use_ {
return new Builder\Use_($name, Use_::TYPE_FUNCTION);
}

/**
@param
@return



*/
public function useConst($name) : Builder\Use_ {
return new Builder\Use_($name, Use_::TYPE_CONSTANT);
}

/**
@param
@return



*/
public function val($value) : Expr {
return BuilderHelpers::normalizeValue($value);
}

/**
@param
@return



*/
public function var($name) : Expr\Variable {
if (!\is_string($name) && !$name instanceof Expr) {
throw new \LogicException('Variable name must be string or Expr');
}

return new Expr\Variable($name);
}

/**
@param
@return





*/
public function args(array $args) : array {
$normalizedArgs = [];
foreach ($args as $arg) {
if ($arg instanceof Arg) {
$normalizedArgs[] = $arg;
} else {
$normalizedArgs[] = new Arg(BuilderHelpers::normalizeValue($arg));
}
}
return $normalizedArgs;
}

/**
@param
@param
@return



*/
public function funcCall($name, array $args = []) : Expr\FuncCall {
return new Expr\FuncCall(
BuilderHelpers::normalizeNameOrExpr($name),
$this->args($args)
);
}

/**
@param
@param
@param
@return



*/
public function methodCall(Expr $var, $name, array $args = []) : Expr\MethodCall {
return new Expr\MethodCall(
$var,
BuilderHelpers::normalizeIdentifierOrExpr($name),
$this->args($args)
);
}

/**
@param
@param
@param
@return



*/
public function staticCall($class, $name, array $args = []) : Expr\StaticCall {
return new Expr\StaticCall(
BuilderHelpers::normalizeNameOrExpr($class),
BuilderHelpers::normalizeIdentifierOrExpr($name),
$this->args($args)
);
}

/**
@param
@param
@return



*/
public function new($class, array $args = []) : Expr\New_ {
return new Expr\New_(
BuilderHelpers::normalizeNameOrExpr($class),
$this->args($args)
);
}

/**
@param
@return



*/
public function constFetch($name) : Expr\ConstFetch {
return new Expr\ConstFetch(BuilderHelpers::normalizeName($name));
}

/**
@param
@param
@return



*/
public function propertyFetch(Expr $var, $name) : Expr\PropertyFetch {
return new Expr\PropertyFetch($var, BuilderHelpers::normalizeIdentifierOrExpr($name));
}

/**
@param
@param
@return



*/
public function classConstFetch($class, $name): Expr\ClassConstFetch {
return new Expr\ClassConstFetch(
BuilderHelpers::normalizeNameOrExpr($class),
BuilderHelpers::normalizeIdentifier($name)
);
}

/**
@param
@return



*/
public function concat(...$exprs) : Concat {
$numExprs = count($exprs);
if ($numExprs < 2) {
throw new \LogicException('Expected at least two expressions');
}

$lastConcat = $this->normalizeStringExpr($exprs[0]);
for ($i = 1; $i < $numExprs; $i++) {
$lastConcat = new Concat($lastConcat, $this->normalizeStringExpr($exprs[$i]));
}
return $lastConcat;
}

/**
@param
@return
*/
private function normalizeStringExpr($expr) : Expr {
if ($expr instanceof Expr) {
return $expr;
}

if (\is_string($expr)) {
return new String_($expr);
}

throw new \LogicException('Expected string or Expr');
}
}
