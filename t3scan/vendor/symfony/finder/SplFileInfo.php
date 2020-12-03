<?php










namespace Symfony\Component\Finder;

/**
@author


*/
class SplFileInfo extends \SplFileInfo
{
private $relativePath;
private $relativePathname;

/**
@param
@param
@param
*/
public function __construct($file, $relativePath, $relativePathname)
{
parent::__construct($file);
$this->relativePath = $relativePath;
$this->relativePathname = $relativePathname;
}

/**
@return




*/
public function getRelativePath()
{
return $this->relativePath;
}

/**
@return




*/
public function getRelativePathname()
{
return $this->relativePathname;
}

/**
@return
@throws



*/
public function getContents()
{
set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });
$content = file_get_contents($this->getPathname());
restore_error_handler();
if (false === $content) {
throw new \RuntimeException($error);
}

return $content;
}
}
