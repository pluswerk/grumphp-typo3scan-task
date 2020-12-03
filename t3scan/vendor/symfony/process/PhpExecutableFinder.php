<?php










namespace Symfony\Component\Process;

/**
@author
@author


*/
class PhpExecutableFinder
{
private $executableFinder;

public function __construct()
{
$this->executableFinder = new ExecutableFinder();
}

/**
@param
@return



*/
public function find($includeArgs = true)
{
$args = $this->findArguments();
$args = $includeArgs && $args ? ' '.implode(' ', $args) : '';


 if (\defined('HHVM_VERSION')) {
return (getenv('PHP_BINARY') ?: \PHP_BINARY).$args;
}


 if (\PHP_BINARY && \in_array(\PHP_SAPI, ['cli', 'cli-server', 'phpdbg'], true)) {
return \PHP_BINARY.$args;
}

if ($php = getenv('PHP_PATH')) {
if (!@is_executable($php)) {
return false;
}

return $php;
}

if ($php = getenv('PHP_PEAR_PHP_BIN')) {
if (@is_executable($php)) {
return $php;
}
}

if (@is_executable($php = \PHP_BINDIR.('\\' === \DIRECTORY_SEPARATOR ? '\\php.exe' : '/php'))) {
return $php;
}

$dirs = [\PHP_BINDIR];
if ('\\' === \DIRECTORY_SEPARATOR) {
$dirs[] = 'C:\xampp\php\\';
}

return $this->executableFinder->find('php', false, $dirs);
}

/**
@return


*/
public function findArguments()
{
$arguments = [];

if (\defined('HHVM_VERSION')) {
$arguments[] = '--php';
} elseif ('phpdbg' === \PHP_SAPI) {
$arguments[] = '-qrr';
}

return $arguments;
}
}
