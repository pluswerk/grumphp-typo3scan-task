<?php











namespace Twig\Error;

/**
@author


*/
class SyntaxError extends Error
{
/**
@param
@param


*/
public function addSuggestions($name, array $items)
{
$alternatives = [];
foreach ($items as $item) {
$lev = levenshtein($name, $item);
if ($lev <= \strlen($name) / 3 || false !== strpos($item, $name)) {
$alternatives[$item] = $lev;
}
}

if (!$alternatives) {
return;
}

asort($alternatives);

$this->appendMessage(sprintf(' Did you mean "%s"?', implode('", "', array_keys($alternatives))));
}
}

class_alias('Twig\Error\SyntaxError', 'Twig_Error_Syntax');
