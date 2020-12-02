<?php











namespace Twig\TokenParser;

use Twig\Node\IncludeNode;
use Twig\Token;








class IncludeTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$expr = $this->parser->getExpressionParser()->parseExpression();

list($variables, $only, $ignoreMissing) = $this->parseArguments();

return new IncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
}

protected function parseArguments()
{
$stream = $this->parser->getStream();

$ignoreMissing = false;
if ($stream->nextIf( 5, 'ignore')) {
$stream->expect( 5, 'missing');

$ignoreMissing = true;
}

$variables = null;
if ($stream->nextIf( 5, 'with')) {
$variables = $this->parser->getExpressionParser()->parseExpression();
}

$only = false;
if ($stream->nextIf( 5, 'only')) {
$only = true;
}

$stream->expect( 3);

return [$variables, $only, $ignoreMissing];
}

public function getTag()
{
return 'include';
}
}

class_alias('Twig\TokenParser\IncludeTokenParser', 'Twig_TokenParser_Include');
