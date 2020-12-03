<?php










namespace Twig\TokenParser;

use Twig\Error\SyntaxError;
use Twig\Node\BodyNode;
use Twig\Node\MacroNode;
use Twig\Node\Node;
use Twig\Token;








final class MacroTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$lineno = $token->getLine();
$stream = $this->parser->getStream();
$name = $stream->expect( 5)->getValue();

$arguments = $this->parser->getExpressionParser()->parseArguments(true, true);

$stream->expect( 3);
$this->parser->pushLocalScope();
$body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
if ($token = $stream->nextIf( 5)) {
$value = $token->getValue();

if ($value != $name) {
throw new SyntaxError(sprintf('Expected endmacro for macro "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
}
}
$this->parser->popLocalScope();
$stream->expect( 3);

$this->parser->setMacro($name, new MacroNode($name, new BodyNode([$body]), $arguments, $lineno, $this->getTag()));

return new Node();
}

public function decideBlockEnd(Token $token)
{
return $token->test('endmacro');
}

public function getTag()
{
return 'macro';
}
}

class_alias('Twig\TokenParser\MacroTokenParser', 'Twig_TokenParser_Macro');
