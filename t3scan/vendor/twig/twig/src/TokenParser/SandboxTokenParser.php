<?php










namespace Twig\TokenParser;

use Twig\Error\SyntaxError;
use Twig\Node\IncludeNode;
use Twig\Node\SandboxNode;
use Twig\Node\TextNode;
use Twig\Token;

/**
@see






*/
final class SandboxTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$stream = $this->parser->getStream();
$stream->expect( 3);
$body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
$stream->expect( 3);


 if (!$body instanceof IncludeNode) {
foreach ($body as $node) {
if ($node instanceof TextNode && ctype_space($node->getAttribute('data'))) {
continue;
}

if (!$node instanceof IncludeNode) {
throw new SyntaxError('Only "include" tags are allowed within a "sandbox" section.', $node->getTemplateLine(), $stream->getSourceContext());
}
}
}

return new SandboxNode($body, $token->getLine(), $this->getTag());
}

public function decideBlockEnd(Token $token)
{
return $token->test('endsandbox');
}

public function getTag()
{
return 'sandbox';
}
}

class_alias('Twig\TokenParser\SandboxTokenParser', 'Twig_TokenParser_Sandbox');
