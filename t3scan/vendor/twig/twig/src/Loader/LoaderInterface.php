<?php










namespace Twig\Loader;

use Twig\Error\LoaderError;
use Twig\Source;

/**
@author


*/
interface LoaderInterface
{
/**
@param
@return
@throws




*/
public function getSourceContext($name);

/**
@param
@return
@throws




*/
public function getCacheKey($name);

/**
@param
@param
@return
@throws





*/
public function isFresh($name, $time);

/**
@param
@return



*/
public function exists($name);
}

class_alias('Twig\Loader\LoaderInterface', 'Twig_LoaderInterface');
