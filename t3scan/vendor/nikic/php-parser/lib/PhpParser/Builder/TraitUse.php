<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser\Builder;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class TraitUse implements Builder
{
protected $traits = [];
protected $adaptations = [];

/**
@param


*/
public function __construct(...$traits) {
foreach ($traits as $trait) {
$this->and($trait);
}
}

/**
@param
@return



*/
public function and($trait) {
$this->traits[] = BuilderHelpers::normalizeName($trait);
return $this;
}

/**
@param
@return



*/
public function with($adaptation) {
$adaptation = BuilderHelpers::normalizeNode($adaptation);

if (!$adaptation instanceof Stmt\TraitUseAdaptation) {
throw new \LogicException('Adaptation must have type TraitUseAdaptation');
}

$this->adaptations[] = $adaptation;
return $this;
}

/**
@return


*/
public function getNode() : Node {
return new Stmt\TraitUse($this->traits, $this->adaptations);
}
}
