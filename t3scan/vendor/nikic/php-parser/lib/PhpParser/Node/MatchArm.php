<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class MatchArm extends NodeAbstract
{
/**
@var */
public $conds;
/**
@var */
public $body;

/**
@param
*/
public function __construct($conds, Node\Expr $body, array $attributes = []) {
$this->conds = $conds;
$this->body = $body;
$this->attributes = $attributes;
}

public function getSubNodeNames() : array {
return ['conds', 'body'];
}

public function getType() : string {
return 'MatchArm';
}
}
