<?php










namespace Twig\RuntimeLoader;

/**
@author


*/
class FactoryRuntimeLoader implements RuntimeLoaderInterface
{
private $map;

/**
@param
*/
public function __construct(array $map = [])
{
$this->map = $map;
}

public function load($class)
{
if (isset($this->map[$class])) {
$runtimeFactory = $this->map[$class];

return $runtimeFactory();
}
}
}

class_alias('Twig\RuntimeLoader\FactoryRuntimeLoader', 'Twig_FactoryRuntimeLoader');
