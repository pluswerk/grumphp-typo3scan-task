<?php










namespace Twig;

/**
@author


*/
class Markup implements \Countable, \JsonSerializable
{
private $content;
private $charset;

public function __construct($content, $charset)
{
$this->content = (string) $content;
$this->charset = $charset;
}

public function __toString()
{
return $this->content;
}

public function count()
{
return mb_strlen($this->content, $this->charset);
}

public function jsonSerialize()
{
return $this->content;
}
}

class_alias('Twig\Markup', 'Twig_Markup');
