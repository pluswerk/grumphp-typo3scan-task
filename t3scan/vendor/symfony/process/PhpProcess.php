<?php










namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\RuntimeException;

/**
@author






*/
class PhpProcess extends Process
{
/**
@param
@param
@param
@param
@param
*/
public function __construct($script, $cwd = null, array $env = null, $timeout = 60, array $options = null)
{
$executableFinder = new PhpExecutableFinder();
if (false === $php = $executableFinder->find(false)) {
$php = null;
} else {
$php = array_merge([$php], $executableFinder->findArguments());
}
if ('phpdbg' === \PHP_SAPI) {
$file = tempnam(sys_get_temp_dir(), 'dbg');
file_put_contents($file, $script);
register_shutdown_function('unlink', $file);
$php[] = $file;
$script = null;
}
if (null !== $options) {
@trigger_error(sprintf('The $options parameter of the %s constructor is deprecated since Symfony 3.3 and will be removed in 4.0.', __CLASS__), \E_USER_DEPRECATED);
}

parent::__construct($php, $cwd, $env, $script, $timeout, $options);
}




public function setPhpBinary($php)
{
$this->setCommandLine($php);
}




public function start(callable $callback = null)
{
if (null === $this->getCommandLine()) {
throw new RuntimeException('Unable to find the PHP executable.');
}
$env = 1 < \func_num_args() ? func_get_arg(1) : null;

parent::start($callback, $env);
}
}
