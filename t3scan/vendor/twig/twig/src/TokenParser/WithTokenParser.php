<?php










namespace Twig\TokenParser;

use Twig\Node\WithNode;
use Twig\Token;

/**
@author


*/
final class WithTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$stream = $this->parser->getStream();

$variables = null;
$only = false;
if (!$stream->test( 3)) {
$variables = $this->parser->getExpressionParser()->parseExpression();
$only = (bool) $stream->nextIf( 5, 'only');
}

$stream->expect( 3);

$body = $this->parser->subparse([$this, 'decideWithEnd'], true);

$stream->expect( 3);

return new WithNode($body, $variables, $only, $token->getLine(), $this->getTag());
}

public function decideWithEnd(Token $token)
{
return $token->test('endwith');
}

public function getTag()
{
return 'with';
}
}

class_alias('Twig\TokenParser\WithTokenParser', 'Twig_TokenParser_With');
