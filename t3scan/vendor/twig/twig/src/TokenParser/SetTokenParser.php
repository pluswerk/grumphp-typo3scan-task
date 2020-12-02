<?php










namespace Twig\TokenParser;

use Twig\Error\SyntaxError;
use Twig\Node\SetNode;
use Twig\Token;











final class SetTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$lineno = $token->getLine();
$stream = $this->parser->getStream();
$names = $this->parser->getExpressionParser()->parseAssignmentExpression();

$capture = false;
if ($stream->nextIf( 8, '=')) {
$values = $this->parser->getExpressionParser()->parseMultitargetExpression();

$stream->expect( 3);

if (\count($names) !== \count($values)) {
throw new SyntaxError('When using set, you must have the same number of variables and assignments.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
}
} else {
$capture = true;

if (\count($names) > 1) {
throw new SyntaxError('When using set with a block, you cannot have a multi-target.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
}

$stream->expect( 3);

$values = $this->parser->subparse([$this, 'decideBlockEnd'], true);
$stream->expect( 3);
}

return new SetNode($capture, $names, $values, $lineno, $this->getTag());
}

public function decideBlockEnd(Token $token)
{
return $token->test('endset');
}

public function getTag()
{
return 'set';
}
}

class_alias('Twig\TokenParser\SetTokenParser', 'Twig_TokenParser_Set');
