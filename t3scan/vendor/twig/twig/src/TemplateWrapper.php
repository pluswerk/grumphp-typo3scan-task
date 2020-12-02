<?php










namespace Twig;

/**
@author


*/
final class TemplateWrapper
{
private $env;
private $template;

/**
@internal



*/
public function __construct(Environment $env, Template $template)
{
$this->env = $env;
$this->template = $template;
}

/**
@param


*/
public function render(array $context = []): string
{

 
 return $this->template->render($context, \func_get_args()[1] ?? []);
}

/**
@param


*/
public function display(array $context = [])
{

 
 $this->template->display($context, \func_get_args()[1] ?? []);
}

/**
@param
@param


*/
public function hasBlock(string $name, array $context = []): bool
{
return $this->template->hasBlock($name, $context);
}

/**
@param
@return



*/
public function getBlockNames(array $context = []): array
{
return $this->template->getBlockNames($context);
}

/**
@param
@param
@return



*/
public function renderBlock(string $name, array $context = []): string
{
$context = $this->env->mergeGlobals($context);
$level = ob_get_level();
if ($this->env->isDebug()) {
ob_start();
} else {
ob_start(function () { return ''; });
}
try {
$this->template->displayBlock($name, $context);
} catch (\Throwable $e) {
while (ob_get_level() > $level) {
ob_end_clean();
}

throw $e;
}

return ob_get_clean();
}

/**
@param
@param


*/
public function displayBlock(string $name, array $context = [])
{
$this->template->displayBlock($name, $this->env->mergeGlobals($context));
}

public function getSourceContext(): Source
{
return $this->template->getSourceContext();
}

public function getTemplateName(): string
{
return $this->template->getTemplateName();
}

/**
@internal
@return

*/
public function unwrap()
{
return $this->template;
}
}

class_alias('Twig\TemplateWrapper', 'Twig_TemplateWrapper');
