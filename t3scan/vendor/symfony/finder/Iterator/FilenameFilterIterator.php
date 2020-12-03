<?php










namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Glob;

/**
@author


*/
class FilenameFilterIterator extends MultiplePcreFilterIterator
{
/**
@return


*/
public function accept()
{
return $this->isAccepted($this->current()->getFilename());
}

/**
@param
@return






*/
protected function toRegex($str)
{
return $this->isRegex($str) ? $str : Glob::toRegex($str);
}
}
