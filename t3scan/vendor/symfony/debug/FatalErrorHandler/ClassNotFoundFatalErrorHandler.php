<?php










namespace Symfony\Component\Debug\FatalErrorHandler;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Symfony\Component\ClassLoader\ClassLoader as SymfonyClassLoader;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\Exception\ClassNotFoundException;
use Symfony\Component\Debug\Exception\FatalErrorException;

/**
@author


*/
class ClassNotFoundFatalErrorHandler implements FatalErrorHandlerInterface
{



public function handleError(array $error, FatalErrorException $exception)
{
if (!preg_match('/^(Class|Interface|Trait) [\'"]([^\'"]+)[\'"] not found$/', $error['message'], $matches)) {
return null;
}
$typeName = strtolower($matches[1]);
$fullyQualifiedClassName = $matches[2];

if (false !== $namespaceSeparatorIndex = strrpos($fullyQualifiedClassName, '\\')) {
$className = substr($fullyQualifiedClassName, $namespaceSeparatorIndex + 1);
$namespacePrefix = substr($fullyQualifiedClassName, 0, $namespaceSeparatorIndex);
$message = sprintf('Attempted to load %s "%s" from namespace "%s".', $typeName, $className, $namespacePrefix);
$tail = ' for another namespace?';
} else {
$className = $fullyQualifiedClassName;
$message = sprintf('Attempted to load %s "%s" from the global namespace.', $typeName, $className);
$tail = '?';
}

if ($candidates = $this->getClassCandidates($className)) {
$tail = array_pop($candidates).'"?';
if ($candidates) {
$tail = ' for e.g. "'.implode('", "', $candidates).'" or "'.$tail;
} else {
$tail = ' for "'.$tail;
}
}
$message .= "\nDid you forget a \"use\" statement".$tail;

return new ClassNotFoundException($message, $exception);
}

/**
@param
@return






*/
private function getClassCandidates($class)
{
if (!\is_array($functions = spl_autoload_functions())) {
return [];
}


 $classes = [];

foreach ($functions as $function) {
if (!\is_array($function)) {
continue;
}

 if ($function[0] instanceof DebugClassLoader) {
$function = $function[0]->getClassLoader();

if (!\is_array($function)) {
continue;
}
}

if ($function[0] instanceof ComposerClassLoader || $function[0] instanceof SymfonyClassLoader) {
foreach ($function[0]->getPrefixes() as $prefix => $paths) {
foreach ($paths as $path) {
$classes = array_merge($classes, $this->findClassInPath($path, $class, $prefix));
}
}
}
if ($function[0] instanceof ComposerClassLoader) {
foreach ($function[0]->getPrefixesPsr4() as $prefix => $paths) {
foreach ($paths as $path) {
$classes = array_merge($classes, $this->findClassInPath($path, $class, $prefix));
}
}
}
}

return array_unique($classes);
}

/**
@param
@param
@param
@return

*/
private function findClassInPath($path, $class, $prefix)
{
if (!$path = realpath($path.'/'.strtr($prefix, '\\_', '//')) ?: realpath($path.'/'.\dirname(strtr($prefix, '\\_', '//'))) ?: realpath($path)) {
return [];
}

$classes = [];
$filename = $class.'.php';
foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
if ($filename == $file->getFileName() && $class = $this->convertFileToClass($path, $file->getPathName(), $prefix)) {
$classes[] = $class;
}
}

return $classes;
}

/**
@param
@param
@param
@return

*/
private function convertFileToClass($path, $file, $prefix)
{
$candidates = [

 $namespacedClass = str_replace([$path.\DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $file),

 $prefix.$namespacedClass,

 $prefix.'\\'.$namespacedClass,

 str_replace('\\', '_', $namespacedClass),

 str_replace('\\', '_', $prefix.$namespacedClass),

 str_replace('\\', '_', $prefix.'\\'.$namespacedClass),
];

if ($prefix) {
$candidates = array_filter($candidates, function ($candidate) use ($prefix) { return 0 === strpos($candidate, $prefix); });
}


 
 
 foreach ($candidates as $candidate) {
if ($this->classExists($candidate)) {
return $candidate;
}
}

try {
require_once $file;
} catch (\Throwable $e) {
return null;
}

foreach ($candidates as $candidate) {
if ($this->classExists($candidate)) {
return $candidate;
}
}

return null;
}

/**
@param
@return

*/
private function classExists($class)
{
return class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);
}
}
