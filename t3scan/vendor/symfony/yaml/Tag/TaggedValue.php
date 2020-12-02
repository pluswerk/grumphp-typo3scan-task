<?php










namespace Symfony\Component\Yaml\Tag;

/**
@author
@author
*/
final class TaggedValue
{
private $tag;
private $value;

/**
@param
@param
*/
public function __construct($tag, $value)
{
$this->tag = $tag;
$this->value = $value;
}

/**
@return
*/
public function getTag()
{
return $this->tag;
}

/**
@return
*/
public function getValue()
{
return $this->value;
}
}
