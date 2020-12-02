<?php










namespace Twig\TokenParser;

use Twig\Parser;

/**
@author


*/
abstract class AbstractTokenParser implements TokenParserInterface
{
/**
@var
*/
protected $parser;

public function setParser(Parser $parser)
{
$this->parser = $parser;
}
}

class_alias('Twig\TokenParser\AbstractTokenParser', 'Twig_TokenParser');
