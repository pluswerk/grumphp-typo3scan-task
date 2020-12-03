<?php










namespace Twig\Loader;

use Twig\Error\LoaderError;

/**
@author


*/
final class ChainLoader implements LoaderInterface, ExistsLoaderInterface, SourceContextLoaderInterface
{
private $hasSourceCache = [];
private $loaders = [];

/**
@param
*/
public function __construct(array $loaders = [])
{
foreach ($loaders as $loader) {
$this->addLoader($loader);
}
}

public function addLoader(LoaderInterface $loader)
{
$this->loaders[] = $loader;
$this->hasSourceCache = [];
}

/**
@return
*/
public function getLoaders()
{
return $this->loaders;
}

public function getSourceContext($name)
{
$exceptions = [];
foreach ($this->loaders as $loader) {
if (!$loader->exists($name)) {
continue;
}

try {
return $loader->getSourceContext($name);
} catch (LoaderError $e) {
$exceptions[] = $e->getMessage();
}
}

throw new LoaderError(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
}

public function exists($name)
{
if (isset($this->hasSourceCache[$name])) {
return $this->hasSourceCache[$name];
}

foreach ($this->loaders as $loader) {
if ($loader->exists($name)) {
return $this->hasSourceCache[$name] = true;
}
}

return $this->hasSourceCache[$name] = false;
}

public function getCacheKey($name)
{
$exceptions = [];
foreach ($this->loaders as $loader) {
if (!$loader->exists($name)) {
continue;
}

try {
return $loader->getCacheKey($name);
} catch (LoaderError $e) {
$exceptions[] = \get_class($loader).': '.$e->getMessage();
}
}

throw new LoaderError(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
}

public function isFresh($name, $time)
{
$exceptions = [];
foreach ($this->loaders as $loader) {
if (!$loader->exists($name)) {
continue;
}

try {
return $loader->isFresh($name, $time);
} catch (LoaderError $e) {
$exceptions[] = \get_class($loader).': '.$e->getMessage();
}
}

throw new LoaderError(sprintf('Template "%s" is not defined%s.', $name, $exceptions ? ' ('.implode(', ', $exceptions).')' : ''));
}
}

class_alias('Twig\Loader\ChainLoader', 'Twig_Loader_Chain');
