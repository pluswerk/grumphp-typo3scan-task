<?php










namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Comparator\NumberComparator;

/**
@author


*/
class SizeRangeFilterIterator extends FilterIterator
{
private $comparators = [];

/**
@param
@param
*/
public function __construct(\Iterator $iterator, array $comparators)
{
$this->comparators = $comparators;

parent::__construct($iterator);
}

/**
@return


*/
public function accept()
{
$fileinfo = $this->current();
if (!$fileinfo->isFile()) {
return true;
}

$filesize = $fileinfo->getSize();
foreach ($this->comparators as $compare) {
if (!$compare->test($filesize)) {
return false;
}
}

return true;
}
}
