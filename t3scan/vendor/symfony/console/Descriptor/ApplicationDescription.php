<?php










namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
@author
@internal

*/
class ApplicationDescription
{
const GLOBAL_NAMESPACE = '_global';

private $application;
private $namespace;
private $showHidden;

/**
@var
*/
private $namespaces;

/**
@var
*/
private $commands;

/**
@var
*/
private $aliases;

/**
@param
@param
*/
public function __construct(Application $application, $namespace = null, $showHidden = false)
{
$this->application = $application;
$this->namespace = $namespace;
$this->showHidden = $showHidden;
}

/**
@return
*/
public function getNamespaces()
{
if (null === $this->namespaces) {
$this->inspectApplication();
}

return $this->namespaces;
}

/**
@return
*/
public function getCommands()
{
if (null === $this->commands) {
$this->inspectApplication();
}

return $this->commands;
}

/**
@param
@return
@throws


*/
public function getCommand($name)
{
if (!isset($this->commands[$name]) && !isset($this->aliases[$name])) {
throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
}

return isset($this->commands[$name]) ? $this->commands[$name] : $this->aliases[$name];
}

private function inspectApplication()
{
$this->commands = [];
$this->namespaces = [];

$all = $this->application->all($this->namespace ? $this->application->findNamespace($this->namespace) : null);
foreach ($this->sortCommands($all) as $namespace => $commands) {
$names = [];

/**
@var */
foreach ($commands as $name => $command) {
if (!$command->getName() || (!$this->showHidden && $command->isHidden())) {
continue;
}

if ($command->getName() === $name) {
$this->commands[$name] = $command;
} else {
$this->aliases[$name] = $command;
}

$names[] = $name;
}

$this->namespaces[$namespace] = ['id' => $namespace, 'commands' => $names];
}
}

/**
@return
*/
private function sortCommands(array $commands)
{
$namespacedCommands = [];
$globalCommands = [];
$sortedCommands = [];
foreach ($commands as $name => $command) {
$key = $this->application->extractNamespace($name, 1);
if (\in_array($key, ['', self::GLOBAL_NAMESPACE], true)) {
$globalCommands[$name] = $command;
} else {
$namespacedCommands[$key][$name] = $command;
}
}

if ($globalCommands) {
ksort($globalCommands);
$sortedCommands[self::GLOBAL_NAMESPACE] = $globalCommands;
}

if ($namespacedCommands) {
ksort($namespacedCommands);
foreach ($namespacedCommands as $key => $commandsSet) {
ksort($commandsSet);
$sortedCommands[$key] = $commandsSet;
}
}

return $sortedCommands;
}
}
