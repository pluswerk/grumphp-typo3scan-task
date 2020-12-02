<?php










namespace Twig\TokenParser;

use Twig\Node\DeprecatedNode;
use Twig\Token;

/**
@author
@final






*/
class DeprecatedTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$expr = $this->parser->getExpressionParser()->parseExpression();

$this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

return new DeprecatedNode($expr, $token->getLine(), $this->getTag());
}

public function getTag()
{
return 'deprecated';
}
}

class_alias('Twig\TokenParser\DeprecatedTokenParser', 'Twig_TokenParser_Deprecated');
