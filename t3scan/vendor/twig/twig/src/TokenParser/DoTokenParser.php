<?php










namespace Twig\TokenParser;

use Twig\Node\DoNode;
use Twig\Token;




final class DoTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$expr = $this->parser->getExpressionParser()->parseExpression();

$this->parser->getStream()->expect( 3);

return new DoNode($expr, $token->getLine(), $this->getTag());
}

public function getTag()
{
return 'do';
}
}

class_alias('Twig\TokenParser\DoTokenParser', 'Twig_TokenParser_Do');
