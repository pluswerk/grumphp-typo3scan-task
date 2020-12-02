<?php










namespace Twig\Util;

use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Source;

/**
@author
*/
final class DeprecationCollector
{
private $twig;

public function __construct(Environment $twig)
{
$this->twig = $twig;
}

/**
@param
@param
@return



*/
public function collectDir($dir, $ext = '.twig')
{
$iterator = new \RegexIterator(
new \RecursiveIteratorIterator(
new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::LEAVES_ONLY
), '{'.preg_quote($ext).'$}'
);

return $this->collect(new TemplateDirIterator($iterator));
}

/**
@param
@return



*/
public function collect(\Traversable $iterator)
{
$deprecations = [];
set_error_handler(function ($type, $msg) use (&$deprecations) {
if (E_USER_DEPRECATED === $type) {
$deprecations[] = $msg;
}
});

foreach ($iterator as $name => $contents) {
try {
$this->twig->parse($this->twig->tokenize(new Source($contents, $name)));
} catch (SyntaxError $e) {

 }
}

restore_error_handler();

return $deprecations;
}
}

class_alias('Twig\Util\DeprecationCollector', 'Twig_Util_DeprecationCollector');
