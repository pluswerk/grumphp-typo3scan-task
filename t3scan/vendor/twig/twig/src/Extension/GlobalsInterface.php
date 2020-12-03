<?php










namespace Twig\Extension;

/**
@author





*/
interface GlobalsInterface
{
/**
@return


*/
public function getGlobals();
}

class_alias('Twig\Extension\GlobalsInterface', 'Twig_Extension_GlobalsInterface');
