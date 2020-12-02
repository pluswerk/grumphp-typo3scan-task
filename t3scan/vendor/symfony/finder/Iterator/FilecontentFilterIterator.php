<?php










namespace Symfony\Component\Finder\Iterator;

/**
@author
@author


*/
class FilecontentFilterIterator extends MultiplePcreFilterIterator
{
/**
@return


*/
public function accept()
{
if (!$this->matchRegexps && !$this->noMatchRegexps) {
return true;
}

$fileinfo = $this->current();

if ($fileinfo->isDir() || !$fileinfo->isReadable()) {
return false;
}

$content = $fileinfo->getContents();
if (!$content) {
return false;
}

return $this->isAccepted($content);
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
