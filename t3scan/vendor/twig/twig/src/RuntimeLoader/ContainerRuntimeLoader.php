<?php










namespace Twig\RuntimeLoader;

use Psr\Container\ContainerInterface;

/**
@author
@author




*/
class ContainerRuntimeLoader implements RuntimeLoaderInterface
{
private $container;

public function __construct(ContainerInterface $container)
{
$this->container = $container;
}

public function load($class)
{
if ($this->container->has($class)) {
return $this->container->get($class);
}
}
}

class_alias('Twig\RuntimeLoader\ContainerRuntimeLoader', 'Twig_ContainerRuntimeLoader');
