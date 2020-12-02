<?php










namespace Twig\Loader;

use Twig\Error\LoaderError;
use Twig\Source;

/**
@author









*/
final class ArrayLoader implements LoaderInterface, ExistsLoaderInterface, SourceContextLoaderInterface
{
private $templates = [];

/**
@param
*/
public function __construct(array $templates = [])
{
$this->templates = $templates;
}

/**
@param
@param


*/
public function setTemplate($name, $template)
{
$this->templates[$name] = $template;
}

public function getSourceContext($name)
{
$name = (string) $name;
if (!isset($this->templates[$name])) {
throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
}

return new Source($this->templates[$name], $name);
}

public function exists($name)
{
return isset($this->templates[$name]);
}

public function getCacheKey($name)
{
if (!isset($this->templates[$name])) {
throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
}

return $name.':'.$this->templates[$name];
}

public function isFresh($name, $time)
{
if (!isset($this->templates[$name])) {
throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
}

return true;
}
}

class_alias('Twig\Loader\ArrayLoader', 'Twig_Loader_Array');
