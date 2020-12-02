<?php










namespace Twig\Extension;

abstract class AbstractExtension implements ExtensionInterface
{
public function getTokenParsers()
{
return [];
}

public function getNodeVisitors()
{
return [];
}

public function getFilters()
{
return [];
}

public function getTests()
{
return [];
}

public function getFunctions()
{
return [];
}

public function getOperators()
{
return [];
}
}

class_alias('Twig\Extension\AbstractExtension', 'Twig_Extension');
