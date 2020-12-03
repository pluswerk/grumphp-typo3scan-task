<?php










namespace Twig;

/**
@author








*/
class FileExtensionEscapingStrategy
{
/**
@param
@return



*/
public static function guess($name)
{
if (\in_array(substr($name, -1), ['/', '\\'])) {
return 'html'; 
 }

if ('.twig' === substr($name, -5)) {
$name = substr($name, 0, -5);
}

$extension = pathinfo($name, PATHINFO_EXTENSION);

switch ($extension) {
case 'js':
return 'js';

case 'css':
return 'css';

case 'txt':
return false;

default:
return 'html';
}
}
}

class_alias('Twig\FileExtensionEscapingStrategy', 'Twig_FileExtensionEscapingStrategy');
