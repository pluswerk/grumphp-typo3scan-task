<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;

class Param implements PhpParser\Builder
{
protected $name;

protected $default = null;

/**
@var */
protected $type = null;

protected $byRef = false;

protected $variadic = false;

/**
@param


*/
public function __construct(string $name) {
$this->name = $name;
}

/**
@param
@return



*/
public function setDefault($value) {
$this->default = BuilderHelpers::normalizeValue($value);

return $this;
}

/**
@param
@return



*/
public function setType($type) {
$this->type = BuilderHelpers::normalizeType($type);
if ($this->type == 'void') {
throw new \LogicException('Parameter type cannot be void');
}

return $this;
}

/**
@param
@return
@deprecated




*/
public function setTypeHint($type) {
return $this->setType($type);
}

/**
@return


*/
public function makeByRef() {
$this->byRef = true;

return $this;
}

/**
@return


*/
public function makeVariadic() {
$this->variadic = true;

return $this;
}

/**
@return


*/
public function getNode() : Node {
return new Node\Param(
new Node\Expr\Variable($this->name),
$this->default, $this->type, $this->byRef, $this->variadic
);
}
}
