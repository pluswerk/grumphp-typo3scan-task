<?php










namespace Twig\TokenParser;

use Twig\Error\SyntaxError;
use Twig\Node\AutoEscapeNode;
use Twig\Node\Expression\ConstantExpression;
use Twig\Token;




final class AutoEscapeTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$lineno = $token->getLine();
$stream = $this->parser->getStream();

if ($stream->test( 3)) {
$value = 'html';
} else {
$expr = $this->parser->getExpressionParser()->parseExpression();
if (!$expr instanceof ConstantExpression) {
throw new SyntaxError('An escaping strategy must be a string or false.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
}
$value = $expr->getAttribute('value');
}

$stream->expect( 3);
$body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
$stream->expect( 3);

return new AutoEscapeNode($value, $body, $lineno, $this->getTag());
}

public function decideBlockEnd(Token $token)
{
return $token->test('endautoescape');
}

public function getTag()
{
return 'autoescape';
}
}

class_alias('Twig\TokenParser\AutoEscapeTokenParser', 'Twig_TokenParser_AutoEscape');
