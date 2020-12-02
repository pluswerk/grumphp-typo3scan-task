<?php










namespace Twig\TokenParser;

use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\ImportNode;
use Twig\Token;






final class ImportTokenParser extends AbstractTokenParser
{
public function parse(Token $token)
{
$macro = $this->parser->getExpressionParser()->parseExpression();
$this->parser->getStream()->expect( 5, 'as');
$var = new AssignNameExpression($this->parser->getStream()->expect( 5)->getValue(), $token->getLine());
$this->parser->getStream()->expect( 3);

$this->parser->addImportedSymbol('template', $var->getAttribute('name'));

return new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
}

public function getTag()
{
return 'import';
}
}

class_alias('Twig\TokenParser\ImportTokenParser', 'Twig_TokenParser_Import');
