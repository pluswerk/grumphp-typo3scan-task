<?php










namespace Twig\TokenParser;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\Parser;
use Twig\Token;

/**
@author


*/
interface TokenParserInterface
{



public function setParser(Parser $parser);

/**
@return
@throws



*/
public function parse(Token $token);

/**
@return


*/
public function getTag();
}

class_alias('Twig\TokenParser\TokenParserInterface', 'Twig_TokenParserInterface');


class_exists('Twig\Token');
class_exists('Twig\Parser');
