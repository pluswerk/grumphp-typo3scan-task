<?php

use Twig\Profiler\Node\EnterProfileNode;

class_exists('Twig\Profiler\Node\EnterProfileNode');

@trigger_error(sprintf('Using the "Twig_Profiler_Node_EnterProfile" class is deprecated since Twig version 2.7, use "Twig\Profiler\Node\EnterProfileNode" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_Profiler_Node_EnterProfile extends EnterProfileNode
{
}
}
