<?php










namespace Symfony\Component\Filesystem;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
@author


*/
class Filesystem
{
private static $lastError;

/**
@param
@param
@param
@throws
@throws







*/
public function copy($originFile, $targetFile, $overwriteNewerFiles = false)
{
$originIsLocal = stream_is_local($originFile) || 0 === stripos($originFile, 'file://');
if ($originIsLocal && !is_file($originFile)) {
throw new FileNotFoundException(sprintf('Failed to copy "%s" because file does not exist.', $originFile), 0, null, $originFile);
}

$this->mkdir(\dirname($targetFile));

$doCopy = true;
if (!$overwriteNewerFiles && null === parse_url($originFile, \PHP_URL_HOST) && is_file($targetFile)) {
$doCopy = filemtime($originFile) > filemtime($targetFile);
}

if ($doCopy) {

 if (false === $source = @fopen($originFile, 'r')) {
throw new IOException(sprintf('Failed to copy "%s" to "%s" because source file could not be opened for reading.', $originFile, $targetFile), 0, null, $originFile);
}


 if (false === $target = @fopen($targetFile, 'w', null, stream_context_create(['ftp' => ['overwrite' => true]]))) {
throw new IOException(sprintf('Failed to copy "%s" to "%s" because target file could not be opened for writing.', $originFile, $targetFile), 0, null, $originFile);
}

$bytesCopied = stream_copy_to_stream($source, $target);
fclose($source);
fclose($target);
unset($source, $target);

if (!is_file($targetFile)) {
throw new IOException(sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), 0, null, $originFile);
}

if ($originIsLocal) {

 @chmod($targetFile, fileperms($targetFile) | (fileperms($originFile) & 0111));

if ($bytesCopied !== $bytesOrigin = filesize($originFile)) {
throw new IOException(sprintf('Failed to copy the whole content of "%s" to "%s" (%g of %g bytes copied).', $originFile, $targetFile, $bytesCopied, $bytesOrigin), 0, null, $originFile);
}
}
}
}

/**
@param
@param
@throws



*/
public function mkdir($dirs, $mode = 0777)
{
foreach ($this->toIterable($dirs) as $dir) {
if (is_dir($dir)) {
continue;
}

if (!self::box('mkdir', $dir, $mode, true)) {
if (!is_dir($dir)) {

 if (self::$lastError) {
throw new IOException(sprintf('Failed to create "%s": ', $dir).self::$lastError, 0, null, $dir);
}
throw new IOException(sprintf('Failed to create "%s".', $dir), 0, null, $dir);
}
}
}
}

/**
@param
@return



*/
public function exists($files)
{
$maxPathLength = \PHP_MAXPATHLEN - 2;

foreach ($this->toIterable($files) as $file) {
if (\strlen($file) > $maxPathLength) {
throw new IOException(sprintf('Could not check if file exist because path length exceeds %d characters.', $maxPathLength), 0, null, $file);
}

if (!file_exists($file)) {
return false;
}
}

return true;
}

/**
@param
@param
@param
@throws



*/
public function touch($files, $time = null, $atime = null)
{
foreach ($this->toIterable($files) as $file) {
$touch = $time ? @touch($file, $time, $atime) : @touch($file);
if (true !== $touch) {
throw new IOException(sprintf('Failed to touch "%s".', $file), 0, null, $file);
}
}
}

/**
@param
@throws



*/
public function remove($files)
{
if ($files instanceof \Traversable) {
$files = iterator_to_array($files, false);
} elseif (!\is_array($files)) {
$files = [$files];
}
$files = array_reverse($files);
foreach ($files as $file) {
if (is_link($file)) {

 if (!(self::box('unlink', $file) || '\\' !== \DIRECTORY_SEPARATOR || self::box('rmdir', $file)) && file_exists($file)) {
throw new IOException(sprintf('Failed to remove symlink "%s": ', $file).self::$lastError);
}
} elseif (is_dir($file)) {
$this->remove(new \FilesystemIterator($file, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS));

if (!self::box('rmdir', $file) && file_exists($file)) {
throw new IOException(sprintf('Failed to remove directory "%s": ', $file).self::$lastError);
}
} elseif (!self::box('unlink', $file) && file_exists($file)) {
throw new IOException(sprintf('Failed to remove file "%s": ', $file).self::$lastError);
}
}
}

/**
@param
@param
@param
@param
@throws



*/
public function chmod($files, $mode, $umask = 0000, $recursive = false)
{
foreach ($this->toIterable($files) as $file) {
if ((\PHP_VERSION_ID < 80000 || \is_int($mode)) && true !== @chmod($file, $mode & ~$umask)) {
throw new IOException(sprintf('Failed to chmod file "%s".', $file), 0, null, $file);
}
if ($recursive && is_dir($file) && !is_link($file)) {
$this->chmod(new \FilesystemIterator($file), $mode, $umask, true);
}
}
}

/**
@param
@param
@param
@throws



*/
public function chown($files, $user, $recursive = false)
{
foreach ($this->toIterable($files) as $file) {
if ($recursive && is_dir($file) && !is_link($file)) {
$this->chown(new \FilesystemIterator($file), $user, true);
}
if (is_link($file) && \function_exists('lchown')) {
if (true !== @lchown($file, $user)) {
throw new IOException(sprintf('Failed to chown file "%s".', $file), 0, null, $file);
}
} else {
if (true !== @chown($file, $user)) {
throw new IOException(sprintf('Failed to chown file "%s".', $file), 0, null, $file);
}
}
}
}

/**
@param
@param
@param
@throws



*/
public function chgrp($files, $group, $recursive = false)
{
foreach ($this->toIterable($files) as $file) {
if ($recursive && is_dir($file) && !is_link($file)) {
$this->chgrp(new \FilesystemIterator($file), $group, true);
}
if (is_link($file) && \function_exists('lchgrp')) {
if (true !== @lchgrp($file, $group) || (\defined('HHVM_VERSION') && !posix_getgrnam($group))) {
throw new IOException(sprintf('Failed to chgrp file "%s".', $file), 0, null, $file);
}
} else {
if (true !== @chgrp($file, $group)) {
throw new IOException(sprintf('Failed to chgrp file "%s".', $file), 0, null, $file);
}
}
}
}

/**
@param
@param
@param
@throws
@throws



*/
public function rename($origin, $target, $overwrite = false)
{

 if (!$overwrite && $this->isReadable($target)) {
throw new IOException(sprintf('Cannot rename because the target "%s" already exists.', $target), 0, null, $target);
}

if (true !== @rename($origin, $target)) {
if (is_dir($origin)) {

 $this->mirror($origin, $target, null, ['override' => $overwrite, 'delete' => $overwrite]);
$this->remove($origin);

return;
}
throw new IOException(sprintf('Cannot rename "%s" to "%s".', $origin, $target), 0, null, $target);
}
}

/**
@param
@return
@throws




*/
private function isReadable($filename)
{
$maxPathLength = \PHP_MAXPATHLEN - 2;

if (\strlen($filename) > $maxPathLength) {
throw new IOException(sprintf('Could not check if file is readable because path length exceeds %d characters.', $maxPathLength), 0, null, $filename);
}

return is_readable($filename);
}

/**
@param
@param
@param
@throws



*/
public function symlink($originDir, $targetDir, $copyOnWindows = false)
{
if ('\\' === \DIRECTORY_SEPARATOR) {
$originDir = strtr($originDir, '/', '\\');
$targetDir = strtr($targetDir, '/', '\\');

if ($copyOnWindows) {
$this->mirror($originDir, $targetDir);

return;
}
}

$this->mkdir(\dirname($targetDir));

if (is_link($targetDir)) {
if (readlink($targetDir) === $originDir) {
return;
}
$this->remove($targetDir);
}

if (!self::box('symlink', $originDir, $targetDir)) {
$this->linkException($originDir, $targetDir, 'symbolic');
}
}

/**
@param
@param
@throws
@throws



*/
public function hardlink($originFile, $targetFiles)
{
if (!$this->exists($originFile)) {
throw new FileNotFoundException(null, 0, null, $originFile);
}

if (!is_file($originFile)) {
throw new FileNotFoundException(sprintf('Origin file "%s" is not a file.', $originFile));
}

foreach ($this->toIterable($targetFiles) as $targetFile) {
if (is_file($targetFile)) {
if (fileinode($originFile) === fileinode($targetFile)) {
continue;
}
$this->remove($targetFile);
}

if (!self::box('link', $originFile, $targetFile)) {
$this->linkException($originFile, $targetFile, 'hard');
}
}
}

/**
@param
@param
@param
*/
private function linkException($origin, $target, $linkType)
{
if (self::$lastError) {
if ('\\' === \DIRECTORY_SEPARATOR && false !== strpos(self::$lastError, 'error code(1314)')) {
throw new IOException(sprintf('Unable to create "%s" link due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?', $linkType), 0, null, $target);
}
}
throw new IOException(sprintf('Failed to create "%s" link from "%s" to "%s".', $linkType, $origin, $target), 0, null, $target);
}

/**
@param
@param
@return











*/
public function readlink($path, $canonicalize = false)
{
if (!$canonicalize && !is_link($path)) {
return null;
}

if ($canonicalize) {
if (!$this->exists($path)) {
return null;
}

if ('\\' === \DIRECTORY_SEPARATOR) {
$path = readlink($path);
}

return realpath($path);
}

if ('\\' === \DIRECTORY_SEPARATOR) {
return realpath($path);
}

return readlink($path);
}

/**
@param
@param
@return



*/
public function makePathRelative($endPath, $startPath)
{
if (!$this->isAbsolutePath($endPath) || !$this->isAbsolutePath($startPath)) {
@trigger_error(sprintf('Support for passing relative paths to %s() is deprecated since Symfony 3.4 and will be removed in 4.0.', __METHOD__), \E_USER_DEPRECATED);
}


 if ('\\' === \DIRECTORY_SEPARATOR) {
$endPath = str_replace('\\', '/', $endPath);
$startPath = str_replace('\\', '/', $startPath);
}

$splitDriveLetter = function ($path) {
return (\strlen($path) > 2 && ':' === $path[1] && '/' === $path[2] && ctype_alpha($path[0]))
? [substr($path, 2), strtoupper($path[0])]
: [$path, null];
};

$splitPath = function ($path, $absolute) {
$result = [];

foreach (explode('/', trim($path, '/')) as $segment) {
if ('..' === $segment && ($absolute || \count($result))) {
array_pop($result);
} elseif ('.' !== $segment && '' !== $segment) {
$result[] = $segment;
}
}

return $result;
};

list($endPath, $endDriveLetter) = $splitDriveLetter($endPath);
list($startPath, $startDriveLetter) = $splitDriveLetter($startPath);

$startPathArr = $splitPath($startPath, static::isAbsolutePath($startPath));
$endPathArr = $splitPath($endPath, static::isAbsolutePath($endPath));

if ($endDriveLetter && $startDriveLetter && $endDriveLetter != $startDriveLetter) {

 return $endDriveLetter.':/'.($endPathArr ? implode('/', $endPathArr).'/' : '');
}


 $index = 0;
while (isset($startPathArr[$index]) && isset($endPathArr[$index]) && $startPathArr[$index] === $endPathArr[$index]) {
++$index;
}


 if (1 === \count($startPathArr) && '' === $startPathArr[0]) {
$depth = 0;
} else {
$depth = \count($startPathArr) - $index;
}


 $traverser = str_repeat('../', $depth);

$endPathRemainder = implode('/', \array_slice($endPathArr, $index));


 $relativePath = $traverser.('' !== $endPathRemainder ? $endPathRemainder.'/' : '');

return '' === $relativePath ? './' : $relativePath;
}

/**
@param
@param
@param
@param
@throws












*/
public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = [])
{
$targetDir = rtrim($targetDir, '/\\');
$originDir = rtrim($originDir, '/\\');
$originDirLen = \strlen($originDir);


 if ($this->exists($targetDir) && isset($options['delete']) && $options['delete']) {
$deleteIterator = $iterator;
if (null === $deleteIterator) {
$flags = \FilesystemIterator::SKIP_DOTS;
$deleteIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($targetDir, $flags), \RecursiveIteratorIterator::CHILD_FIRST);
}
$targetDirLen = \strlen($targetDir);
foreach ($deleteIterator as $file) {
$origin = $originDir.substr($file->getPathname(), $targetDirLen);
if (!$this->exists($origin)) {
$this->remove($file);
}
}
}

$copyOnWindows = false;
if (isset($options['copy_on_windows'])) {
$copyOnWindows = $options['copy_on_windows'];
}

if (null === $iterator) {
$flags = $copyOnWindows ? \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS : \FilesystemIterator::SKIP_DOTS;
$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($originDir, $flags), \RecursiveIteratorIterator::SELF_FIRST);
}

if ($this->exists($originDir)) {
$this->mkdir($targetDir);
}

foreach ($iterator as $file) {
$target = $targetDir.substr($file->getPathname(), $originDirLen);

if ($copyOnWindows) {
if (is_file($file)) {
$this->copy($file, $target, isset($options['override']) ? $options['override'] : false);
} elseif (is_dir($file)) {
$this->mkdir($target);
} else {
throw new IOException(sprintf('Unable to guess "%s" file type.', $file), 0, null, $file);
}
} else {
if (is_link($file)) {
$this->symlink($file->getLinkTarget(), $target);
} elseif (is_dir($file)) {
$this->mkdir($target);
} elseif (is_file($file)) {
$this->copy($file, $target, isset($options['override']) ? $options['override'] : false);
} else {
throw new IOException(sprintf('Unable to guess "%s" file type.', $file), 0, null, $file);
}
}
}
}

/**
@param
@return



*/
public function isAbsolutePath($file)
{
return strspn($file, '/\\', 0, 1)
|| (\strlen($file) > 3 && ctype_alpha($file[0])
&& ':' === $file[1]
&& strspn($file, '/\\', 2, 1)
)
|| null !== parse_url($file, \PHP_URL_SCHEME)
;
}

/**
@param
@param
@return




*/
public function tempnam($dir, $prefix)
{
list($scheme, $hierarchy) = $this->getSchemeAndHierarchy($dir);


 if (null === $scheme || 'file' === $scheme || 'gs' === $scheme) {
$tmpFile = @tempnam($hierarchy, $prefix);


 if (false !== $tmpFile) {
if (null !== $scheme && 'gs' !== $scheme) {
return $scheme.'://'.$tmpFile;
}

return $tmpFile;
}

throw new IOException('A temporary file could not be created.');
}


 for ($i = 0; $i < 10; ++$i) {

 $tmpFile = $dir.'/'.$prefix.uniqid(mt_rand(), true);


 
 $handle = @fopen($tmpFile, 'x+');


 if (false === $handle) {
continue;
}


 @fclose($handle);

return $tmpFile;
}

throw new IOException('A temporary file could not be created.');
}

/**
@param
@param
@throws



*/
public function dumpFile($filename, $content)
{
$dir = \dirname($filename);

if (!is_dir($dir)) {
$this->mkdir($dir);
}

if (!is_writable($dir)) {
throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, null, $dir);
}


 
 $tmpFile = $this->tempnam($dir, basename($filename));

if (false === @file_put_contents($tmpFile, $content)) {
throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
}

@chmod($tmpFile, file_exists($filename) ? fileperms($filename) : 0666 & ~umask());

$this->rename($tmpFile, $filename, true);
}

/**
@param
@param
@throws



*/
public function appendToFile($filename, $content)
{
$dir = \dirname($filename);

if (!is_dir($dir)) {
$this->mkdir($dir);
}

if (!is_writable($dir)) {
throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, null, $dir);
}

if (false === @file_put_contents($filename, $content, \FILE_APPEND)) {
throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
}
}

/**
@param
@return

*/
private function toIterable($files)
{
return \is_array($files) || $files instanceof \Traversable ? $files : [$files];
}

/**
@param
@return



*/
private function getSchemeAndHierarchy($filename)
{
$components = explode('://', $filename, 2);

return 2 === \count($components) ? [$components[0], $components[1]] : [null, $components[0]];
}

/**
@param
@return

*/
private static function box($func)
{
self::$lastError = null;
set_error_handler(__CLASS__.'::handleError');
try {
$result = \call_user_func_array($func, \array_slice(\func_get_args(), 1));
restore_error_handler();

return $result;
} catch (\Throwable $e) {
} catch (\Exception $e) {
}
restore_error_handler();

throw $e;
}

/**
@internal
*/
public static function handleError($type, $msg)
{
self::$lastError = $msg;
}
}
