<?php










namespace Symfony\Component\Finder;

use Symfony\Component\Finder\Comparator\DateComparator;
use Symfony\Component\Finder\Comparator\NumberComparator;
use Symfony\Component\Finder\Iterator\CustomFilterIterator;
use Symfony\Component\Finder\Iterator\DateRangeFilterIterator;
use Symfony\Component\Finder\Iterator\DepthRangeFilterIterator;
use Symfony\Component\Finder\Iterator\ExcludeDirectoryFilterIterator;
use Symfony\Component\Finder\Iterator\FilecontentFilterIterator;
use Symfony\Component\Finder\Iterator\FilenameFilterIterator;
use Symfony\Component\Finder\Iterator\SizeRangeFilterIterator;
use Symfony\Component\Finder\Iterator\SortableIterator;

/**
@author










*/
class Finder implements \IteratorAggregate, \Countable
{
const IGNORE_VCS_FILES = 1;
const IGNORE_DOT_FILES = 2;

private $mode = 0;
private $names = [];
private $notNames = [];
private $exclude = [];
private $filters = [];
private $depths = [];
private $sizes = [];
private $followLinks = false;
private $sort = false;
private $ignore = 0;
private $dirs = [];
private $dates = [];
private $iterators = [];
private $contains = [];
private $notContains = [];
private $paths = [];
private $notPaths = [];
private $ignoreUnreadableDirs = false;

private static $vcsPatterns = ['.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg'];

public function __construct()
{
$this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;
}

/**
@return


*/
public static function create()
{
return new static();
}

/**
@return


*/
public function directories()
{
$this->mode = Iterator\FileTypeFilterIterator::ONLY_DIRECTORIES;

return $this;
}

/**
@return


*/
public function files()
{
$this->mode = Iterator\FileTypeFilterIterator::ONLY_FILES;

return $this;
}

/**
@param
@return
@see
@see









*/
public function depth($level)
{
$this->depths[] = new Comparator\NumberComparator($level);

return $this;
}

/**
@param
@return
@see
@see
@see











*/
public function date($date)
{
$this->dates[] = new Comparator\DateComparator($date);

return $this;
}

/**
@param
@return
@see










*/
public function name($pattern)
{
$this->names[] = $pattern;

return $this;
}

/**
@param
@return
@see




*/
public function notName($pattern)
{
$this->notNames[] = $pattern;

return $this;
}

/**
@param
@return
@see









*/
public function contains($pattern)
{
$this->contains[] = $pattern;

return $this;
}

/**
@param
@return
@see









*/
public function notContains($pattern)
{
$this->notContains[] = $pattern;

return $this;
}

/**
@param
@return
@see











*/
public function path($pattern)
{
$this->paths[] = $pattern;

return $this;
}

/**
@param
@return
@see











*/
public function notPath($pattern)
{
$this->notPaths[] = $pattern;

return $this;
}

/**
@param
@return
@see
@see








*/
public function size($size)
{
$this->sizes[] = new Comparator\NumberComparator($size);

return $this;
}

/**
@param
@return
@see








*/
public function exclude($dirs)
{
$this->exclude = array_merge($this->exclude, (array) $dirs);

return $this;
}

/**
@param
@return
@see






*/
public function ignoreDotFiles($ignoreDotFiles)
{
if ($ignoreDotFiles) {
$this->ignore |= static::IGNORE_DOT_FILES;
} else {
$this->ignore &= ~static::IGNORE_DOT_FILES;
}

return $this;
}

/**
@param
@return
@see






*/
public function ignoreVCS($ignoreVCS)
{
if ($ignoreVCS) {
$this->ignore |= static::IGNORE_VCS_FILES;
} else {
$this->ignore &= ~static::IGNORE_VCS_FILES;
}

return $this;
}

/**
@see
@param



*/
public static function addVCSPattern($pattern)
{
foreach ((array) $pattern as $p) {
self::$vcsPatterns[] = $p;
}

self::$vcsPatterns = array_unique(self::$vcsPatterns);
}

/**
@return
@see







*/
public function sort(\Closure $closure)
{
$this->sort = $closure;

return $this;
}

/**
@return
@see





*/
public function sortByName()
{
$this->sort = Iterator\SortableIterator::SORT_BY_NAME;

return $this;
}

/**
@return
@see





*/
public function sortByType()
{
$this->sort = Iterator\SortableIterator::SORT_BY_TYPE;

return $this;
}

/**
@return
@see







*/
public function sortByAccessedTime()
{
$this->sort = Iterator\SortableIterator::SORT_BY_ACCESSED_TIME;

return $this;
}

/**
@return
@see









*/
public function sortByChangedTime()
{
$this->sort = Iterator\SortableIterator::SORT_BY_CHANGED_TIME;

return $this;
}

/**
@return
@see







*/
public function sortByModifiedTime()
{
$this->sort = Iterator\SortableIterator::SORT_BY_MODIFIED_TIME;

return $this;
}

/**
@return
@see






*/
public function filter(\Closure $closure)
{
$this->filters[] = $closure;

return $this;
}

/**
@return


*/
public function followLinks()
{
$this->followLinks = true;

return $this;
}

/**
@param
@return





*/
public function ignoreUnreadableDirs($ignore = true)
{
$this->ignoreUnreadableDirs = (bool) $ignore;

return $this;
}

/**
@param
@return
@throws




*/
public function in($dirs)
{
$resolvedDirs = [];

foreach ((array) $dirs as $dir) {
if (is_dir($dir)) {
$resolvedDirs[] = $this->normalizeDir($dir);
} elseif ($glob = glob($dir, (\defined('GLOB_BRACE') ? \GLOB_BRACE : 0) | \GLOB_ONLYDIR | \GLOB_NOSORT)) {
sort($glob);
$resolvedDirs = array_merge($resolvedDirs, array_map([$this, 'normalizeDir'], $glob));
} else {
throw new \InvalidArgumentException(sprintf('The "%s" directory does not exist.', $dir));
}
}

$this->dirs = array_merge($this->dirs, $resolvedDirs);

return $this;
}

/**
@return
@throws





*/
public function getIterator()
{
if (0 === \count($this->dirs) && 0 === \count($this->iterators)) {
throw new \LogicException('You must call one of in() or append() methods before iterating over a Finder.');
}

if (1 === \count($this->dirs) && 0 === \count($this->iterators)) {
return $this->searchInDirectory($this->dirs[0]);
}

$iterator = new \AppendIterator();
foreach ($this->dirs as $dir) {
$iterator->append($this->searchInDirectory($dir));
}

foreach ($this->iterators as $it) {
$iterator->append($it);
}

return $iterator;
}

/**
@param
@return
@throws






*/
public function append($iterator)
{
if ($iterator instanceof \IteratorAggregate) {
$this->iterators[] = $iterator->getIterator();
} elseif ($iterator instanceof \Iterator) {
$this->iterators[] = $iterator;
} elseif ($iterator instanceof \Traversable || \is_array($iterator)) {
$it = new \ArrayIterator();
foreach ($iterator as $file) {
$it->append($file instanceof \SplFileInfo ? $file : new \SplFileInfo($file));
}
$this->iterators[] = $it;
} else {
throw new \InvalidArgumentException('Finder::append() method wrong argument type.');
}

return $this;
}

/**
@return


*/
public function hasResults()
{
foreach ($this->getIterator() as $_) {
return true;
}

return false;
}

/**
@return


*/
public function count()
{
return iterator_count($this->getIterator());
}

/**
@param
@return

*/
private function searchInDirectory($dir)
{
$exclude = $this->exclude;
$notPaths = $this->notPaths;

if (static::IGNORE_VCS_FILES === (static::IGNORE_VCS_FILES & $this->ignore)) {
$exclude = array_merge($exclude, self::$vcsPatterns);
}

if (static::IGNORE_DOT_FILES === (static::IGNORE_DOT_FILES & $this->ignore)) {
$notPaths[] = '#(^|/)\..+(/|$)#';
}

$minDepth = 0;
$maxDepth = \PHP_INT_MAX;

foreach ($this->depths as $comparator) {
switch ($comparator->getOperator()) {
case '>':
$minDepth = $comparator->getTarget() + 1;
break;
case '>=':
$minDepth = $comparator->getTarget();
break;
case '<':
$maxDepth = $comparator->getTarget() - 1;
break;
case '<=':
$maxDepth = $comparator->getTarget();
break;
default:
$minDepth = $maxDepth = $comparator->getTarget();
}
}

$flags = \RecursiveDirectoryIterator::SKIP_DOTS;

if ($this->followLinks) {
$flags |= \RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
}

$iterator = new Iterator\RecursiveDirectoryIterator($dir, $flags, $this->ignoreUnreadableDirs);

if ($exclude) {
$iterator = new Iterator\ExcludeDirectoryFilterIterator($iterator, $exclude);
}

$iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

if ($minDepth > 0 || $maxDepth < \PHP_INT_MAX) {
$iterator = new Iterator\DepthRangeFilterIterator($iterator, $minDepth, $maxDepth);
}

if ($this->mode) {
$iterator = new Iterator\FileTypeFilterIterator($iterator, $this->mode);
}

if ($this->names || $this->notNames) {
$iterator = new Iterator\FilenameFilterIterator($iterator, $this->names, $this->notNames);
}

if ($this->contains || $this->notContains) {
$iterator = new Iterator\FilecontentFilterIterator($iterator, $this->contains, $this->notContains);
}

if ($this->sizes) {
$iterator = new Iterator\SizeRangeFilterIterator($iterator, $this->sizes);
}

if ($this->dates) {
$iterator = new Iterator\DateRangeFilterIterator($iterator, $this->dates);
}

if ($this->filters) {
$iterator = new Iterator\CustomFilterIterator($iterator, $this->filters);
}

if ($this->paths || $notPaths) {
$iterator = new Iterator\PathFilterIterator($iterator, $this->paths, $notPaths);
}

if ($this->sort) {
$iteratorAggregate = new Iterator\SortableIterator($iterator, $this->sort);
$iterator = $iteratorAggregate->getIterator();
}

return $iterator;
}

/**
@param
@return





*/
private function normalizeDir($dir)
{
if ('/' === $dir) {
return $dir;
}

$dir = rtrim($dir, '/'.\DIRECTORY_SEPARATOR);

if (preg_match('#^(ssh2\.)?s?ftp://#', $dir)) {
$dir .= '/';
}

return $dir;
}
}
