<?php










namespace Symfony\Component\Finder\Iterator;

/**
@author


*/
class DepthRangeFilterIterator extends FilterIterator
{
private $minDepth = 0;

/**
@param
@param
@param
*/
public function __construct(\RecursiveIteratorIterator $iterator, $minDepth = 0, $maxDepth = \PHP_INT_MAX)
{
$this->minDepth = $minDepth;
$iterator->setMaxDepth(\PHP_INT_MAX === $maxDepth ? -1 : $maxDepth);

parent::__construct($iterator);
}

/**
@return


*/
public function accept()
{
return $this->getInnerIterator()->getDepth() >= $this->minDepth;
}
}
