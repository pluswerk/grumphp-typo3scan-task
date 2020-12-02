<?php










namespace Twig\Util;

/**
@author
*/
class TemplateDirIterator extends \IteratorIterator
{
public function current()
{
return file_get_contents(parent::current());
}

public function key()
{
return (string) parent::key();
}
}

class_alias('Twig\Util\TemplateDirIterator', 'Twig_Util_TemplateDirIterator');
