<?php










namespace Twig\Cache;

/**
@author


*/
final class NullCache implements CacheInterface
{
public function generateKey($name, $className)
{
return '';
}

public function write($key, $content)
{
}

public function load($key)
{
}

public function getTimestamp($key)
{
return 0;
}
}

class_alias('Twig\Cache\NullCache', 'Twig_Cache_Null');
