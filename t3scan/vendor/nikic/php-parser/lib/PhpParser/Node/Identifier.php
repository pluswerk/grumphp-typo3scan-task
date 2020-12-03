<?php declare(strict_types=1);

namespace PhpParser\Node;

use PhpParser\NodeAbstract;




class Identifier extends NodeAbstract
{
/**
@var */
public $name;

private static $specialClassNames = [
'self' => true,
'parent' => true,
'static' => true,
];

/**
@param
@param


*/
public function __construct(string $name, array $attributes = []) {
$this->attributes = $attributes;
$this->name = $name;
}

public function getSubNodeNames() : array {
return ['name'];
}

/**
@return


*/
public function toString() : string {
return $this->name;
}

/**
@return


*/
public function toLowerString() : string {
return strtolower($this->name);
}

/**
@return


*/
public function isSpecialClassName() : bool {
return isset(self::$specialClassNames[strtolower($this->name)]);
}

/**
@return


*/
public function __toString() : string {
return $this->name;
}

public function getType() : string {
return 'Identifier';
}
}
