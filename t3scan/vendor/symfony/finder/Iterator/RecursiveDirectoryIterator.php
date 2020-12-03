<?php










namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Finder\SplFileInfo;

/**
@author


*/
class RecursiveDirectoryIterator extends \RecursiveDirectoryIterator
{
/**
@var
*/
private $ignoreUnreadableDirs;

/**
@var
*/
private $rewindable;


 private $rootPath;
private $subPath;
private $directorySeparator = '/';

/**
@param
@param
@param
@throws

*/
public function __construct($path, $flags, $ignoreUnreadableDirs = false)
{
if ($flags & (self::CURRENT_AS_PATHNAME | self::CURRENT_AS_SELF)) {
throw new \RuntimeException('This iterator only support returning current as fileinfo.');
}

parent::__construct($path, $flags);
$this->ignoreUnreadableDirs = $ignoreUnreadableDirs;
$this->rootPath = $path;
if ('/' !== \DIRECTORY_SEPARATOR && !($flags & self::UNIX_PATHS)) {
$this->directorySeparator = \DIRECTORY_SEPARATOR;
}
}

/**
@return


*/
public function current()
{


if (null === $subPathname = $this->subPath) {
$subPathname = $this->subPath = (string) $this->getSubPath();
}
if ('' !== $subPathname) {
$subPathname .= $this->directorySeparator;
}
$subPathname .= $this->getFilename();

if ('/' !== $basePath = $this->rootPath) {
$basePath .= $this->directorySeparator;
}

return new SplFileInfo($basePath.$subPathname, $this->subPath, $subPathname);
}

/**
@return
@throws

*/
public function getChildren()
{
try {
$children = parent::getChildren();

if ($children instanceof self) {

 $children->ignoreUnreadableDirs = $this->ignoreUnreadableDirs;


 $children->rewindable = &$this->rewindable;
$children->rootPath = $this->rootPath;
}

return $children;
} catch (\UnexpectedValueException $e) {
if ($this->ignoreUnreadableDirs) {

 return new \RecursiveArrayIterator([]);
} else {
throw new AccessDeniedException($e->getMessage(), $e->getCode(), $e);
}
}
}




public function rewind()
{
if (false === $this->isRewindable()) {
return;
}


 if (\PHP_VERSION_ID < 50523 || \PHP_VERSION_ID >= 50600 && \PHP_VERSION_ID < 50607) {
parent::next();
}

parent::rewind();
}

/**
@return


*/
public function isRewindable()
{
if (null !== $this->rewindable) {
return $this->rewindable;
}


 if ('' === $this->getPath()) {
return $this->rewindable = false;
}

if (false !== $stream = @opendir($this->getPath())) {
$infos = stream_get_meta_data($stream);
closedir($stream);

if ($infos['seekable']) {
return $this->rewindable = true;
}
}

return $this->rewindable = false;
}
}
