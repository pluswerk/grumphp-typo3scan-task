<?php










namespace Twig\Sandbox;

/**
@author


*/
interface SecurityPolicyInterface
{
/**
@throws
*/
public function checkSecurity($tags, $filters, $functions);

/**
@throws
*/
public function checkMethodAllowed($obj, $method);

/**
@throws
*/
public function checkPropertyAllowed($obj, $method);
}

class_alias('Twig\Sandbox\SecurityPolicyInterface', 'Twig_Sandbox_SecurityPolicyInterface');
