<?php










namespace Twig\TokenParser;

use Twig\Node\BlockNode;
use Twig\Node\Expression\BlockReferenceExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\PrintNode;
use Twig\Token;

/**
@deprecated






*/
final class FilterTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$stream = $this->parser->getStream();
$lineno = $token->getLine();

@trigger_error(sprintf('The "filter" tag in "%s" at line %d is deprecated since Twig 2.9, use the "apply" tag instead.', $stream->getSourceContext()->getName(), $lineno), E_USER_DEPRECATED);

$name = $this->parser->getVarName();
$ref = new BlockReferenceExpression(new ConstantExpression($name, $lineno), null, $lineno, $this->getTag());

$filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
$stream->expect( 3);

$body = $this->parser->subparse([$this, 'decideBlockEnd'], true);
$stream->expect( 3);

$block = new BlockNode($name, $body, $lineno);
$this->parser->setBlock($name, $block);

return new PrintNode($filter, $lineno, $this->getTag());
}

public function decideBlockEnd(Token $token)
{
return $token->test('endfilter');
}

public function getTag()
{
return 'filter';
}
}

class_alias('Twig\TokenParser\FilterTokenParser', 'Twig_TokenParser_Filter');
