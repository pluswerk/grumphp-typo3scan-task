<?php










namespace Twig;

/**
@author


*/
final class Source
{
private $code;
private $name;
private $path;

/**
@param
@param
@param
*/
public function __construct(string $code, string $name, string $path = '')
{
$this->code = $code;
$this->name = $name;
$this->path = $path;
}

public function getCode(): string
{
return $this->code;
}

public function getName()
{
return $this->name;
}

public function getPath(): string
{
return $this->path;
}
}

class_alias('Twig\Source', 'Twig_Source');
