<?php










namespace Twig\RuntimeLoader;

/**
@author


*/
interface RuntimeLoaderInterface
{
/**
@param
@return



*/
public function load($class);
}

class_alias('Twig\RuntimeLoader\RuntimeLoaderInterface', 'Twig_RuntimeLoaderInterface');
