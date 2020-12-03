<?php










namespace Twig\TokenParser;

use Twig\Node\FlushNode;
use Twig\Token;

/**
@see


*/
final class FlushTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$this->parser->getStream()->expect( 3);

return new FlushNode($token->getLine(), $this->getTag());
}

public function getTag()
{
return 'flush';
}
}

class_alias('Twig\TokenParser\FlushTokenParser', 'Twig_TokenParser_Flush');
