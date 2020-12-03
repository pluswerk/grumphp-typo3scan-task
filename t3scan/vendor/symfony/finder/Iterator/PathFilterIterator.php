<?php










namespace Symfony\Component\Finder\Iterator;

/**
@author
@author


*/
class PathFilterIterator extends MultiplePcreFilterIterator
{
/**
@return


*/
public function accept()
{
$filename = $this->current()->getRelativePathname();

if ('\\' === \DIRECTORY_SEPARATOR) {
$filename = str_replace('\\', '/', $filename);
}

return $this->isAccepted($filename);
}

/**
@param
@return










*/
protected function toRegex($str)
{
return $this->isRegex($str) ? $str : '/'.preg_quote($str, '/').'/';
}
}
