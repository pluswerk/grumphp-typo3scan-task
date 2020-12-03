<?php










namespace Twig\Extension;

use Twig\Environment;

/**
@author
@deprecated






*/
interface InitRuntimeInterface
{





public function initRuntime(Environment $environment);
}

class_alias('Twig\Extension\InitRuntimeInterface', 'Twig_Extension_InitRuntimeInterface');
