<?php declare(strict_types=1);

namespace PhpParser\Parser;

use PhpParser\Error;
use PhpParser\ErrorHandler;
use PhpParser\Parser;

class Multiple implements Parser
{
/**
@var */
private $parsers;

/**
@param






*/
public function __construct(array $parsers) {
$this->parsers = $parsers;
}

public function parse(string $code, ErrorHandler $errorHandler = null) {
if (null === $errorHandler) {
$errorHandler = new ErrorHandler\Throwing;
}

list($firstStmts, $firstError) = $this->tryParse($this->parsers[0], $errorHandler, $code);
if ($firstError === null) {
return $firstStmts;
}

for ($i = 1, $c = count($this->parsers); $i < $c; ++$i) {
list($stmts, $error) = $this->tryParse($this->parsers[$i], $errorHandler, $code);
if ($error === null) {
return $stmts;
}
}

throw $firstError;
}

private function tryParse(Parser $parser, ErrorHandler $errorHandler, $code) {
$stmts = null;
$error = null;
try {
$stmts = $parser->parse($code, $errorHandler);
} catch (Error $error) {}
return [$stmts, $error];
}
}
