<?php










namespace Twig\TokenParser;

use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\ImportNode;
use Twig\Token;






final class FromTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$macro = $this->parser->getExpressionParser()->parseExpression();
$stream = $this->parser->getStream();
$stream->expect( 5, 'import');

$targets = [];
do {
$name = $stream->expect( 5)->getValue();

$alias = $name;
if ($stream->nextIf('as')) {
$alias = $stream->expect( 5)->getValue();
}

$targets[$name] = $alias;

if (!$stream->nextIf( 9, ',')) {
break;
}
} while (true);

$stream->expect( 3);

$var = new AssignNameExpression($this->parser->getVarName(), $token->getLine());
$node = new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());

foreach ($targets as $name => $alias) {
$this->parser->addImportedSymbol('function', $alias, 'macro_'.$name, $var);
}

return $node;
}

public function getTag()
{
return 'from';
}
}

class_alias('Twig\TokenParser\FromTokenParser', 'Twig_TokenParser_From');
