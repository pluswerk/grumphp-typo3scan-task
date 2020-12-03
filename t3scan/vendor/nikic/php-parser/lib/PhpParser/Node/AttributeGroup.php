<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\Node;
use PhpParser\NodeAbstract;

class AttributeGroup extends NodeAbstract
{
/**
@var */
public $attrs;

/**
@param
@param
*/
public function __construct(array $attrs, array $attributes = []) {
$this->attributes = $attributes;
$this->attrs = $attrs;
}

public function getSubNodeNames() : array {
return ['attrs'];
}

public function getType() : string {
return 'AttributeGroup';
}
}
