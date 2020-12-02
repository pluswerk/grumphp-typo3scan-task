<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\BuilderHelpers;
use PhpParser\Node;

abstract class FunctionLike extends Declaration
{
protected $returnByRef = false;
protected $params = [];

/**
@var */
protected $returnType = null;

/**
@return


*/
public function makeReturnByRef() {
$this->returnByRef = true;

return $this;
}

/**
@param
@return



*/
public function addParam($param) {
$param = BuilderHelpers::normalizeNode($param);

if (!$param instanceof Node\Param) {
throw new \LogicException(sprintf('Expected parameter node, got "%s"', $param->getType()));
}

$this->params[] = $param;

return $this;
}

/**
@param
@return



*/
public function addParams(array $params) {
foreach ($params as $param) {
$this->addParam($param);
}

return $this;
}

/**
@param
@return




*/
public function setReturnType($type) {
$this->returnType = BuilderHelpers::normalizeType($type);

return $this;
}
}
