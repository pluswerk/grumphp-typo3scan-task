<?php










namespace Twig\Cache;

/**
@author






*/
interface CacheInterface
{
/**
@param
@param
@return



*/
public function generateKey($name, $className);

/**
@param
@param


*/
public function write($key, $content);

/**
@param


*/
public function load($key);

/**
@param
@return



*/
public function getTimestamp($key);
}

class_alias('Twig\Cache\CacheInterface', 'Twig_CacheInterface');
