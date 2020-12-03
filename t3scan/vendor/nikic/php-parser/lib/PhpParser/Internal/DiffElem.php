<?php declare(strict_types=1);

namespace PhpParser\Internal;

/**
@internal
*/
class DiffElem
{
const TYPE_KEEP = 0;
const TYPE_REMOVE = 1;
const TYPE_ADD = 2;
const TYPE_REPLACE = 3;

/**
@var */
public $type;
/**
@var */
public $old;
/**
@var */
public $new;

public function __construct(int $type, $old, $new) {
$this->type = $type;
$this->old = $old;
$this->new = $new;
}
}
