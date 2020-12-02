<?php declare(strict_types=1);

namespace PhpParser\Internal;

/**
@internal


*/
class TokenStream
{
/**
@var */
private $tokens;
/**
@var */
private $indentMap;

/**
@param


*/
public function __construct(array $tokens) {
$this->tokens = $tokens;
$this->indentMap = $this->calcIndentMap();
}

/**
@param
@param
@return



*/
public function haveParens(int $startPos, int $endPos) : bool {
return $this->haveTokenImmediatelyBefore($startPos, '(')
&& $this->haveTokenImmediatelyAfter($endPos, ')');
}

/**
@param
@param
@return



*/
public function haveBraces(int $startPos, int $endPos) : bool {
return $this->haveTokenImmediatelyBefore($startPos, '{')
&& $this->haveTokenImmediatelyAfter($endPos, '}');
}

/**
@param
@param
@return





*/
public function haveTokenImmediatelyBefore(int $pos, $expectedTokenType) : bool {
$tokens = $this->tokens;
$pos--;
for (; $pos >= 0; $pos--) {
$tokenType = $tokens[$pos][0];
if ($tokenType === $expectedTokenType) {
return true;
}
if ($tokenType !== \T_WHITESPACE
&& $tokenType !== \T_COMMENT && $tokenType !== \T_DOC_COMMENT) {
break;
}
}
return false;
}

/**
@param
@param
@return





*/
public function haveTokenImmediatelyAfter(int $pos, $expectedTokenType) : bool {
$tokens = $this->tokens;
$pos++;
for (; $pos < \count($tokens); $pos++) {
$tokenType = $tokens[$pos][0];
if ($tokenType === $expectedTokenType) {
return true;
}
if ($tokenType !== \T_WHITESPACE
&& $tokenType !== \T_COMMENT && $tokenType !== \T_DOC_COMMENT) {
break;
}
}
return false;
}

public function skipLeft(int $pos, $skipTokenType) {
$tokens = $this->tokens;

$pos = $this->skipLeftWhitespace($pos);
if ($skipTokenType === \T_WHITESPACE) {
return $pos;
}

if ($tokens[$pos][0] !== $skipTokenType) {

 throw new \Exception('Encountered unexpected token');
}
$pos--;

return $this->skipLeftWhitespace($pos);
}

public function skipRight(int $pos, $skipTokenType) {
$tokens = $this->tokens;

$pos = $this->skipRightWhitespace($pos);
if ($skipTokenType === \T_WHITESPACE) {
return $pos;
}

if ($tokens[$pos][0] !== $skipTokenType) {

 throw new \Exception('Encountered unexpected token');
}
$pos++;

return $this->skipRightWhitespace($pos);
}

/**
@param
@return


*/
public function skipLeftWhitespace(int $pos) {
$tokens = $this->tokens;
for (; $pos >= 0; $pos--) {
$type = $tokens[$pos][0];
if ($type !== \T_WHITESPACE && $type !== \T_COMMENT && $type !== \T_DOC_COMMENT) {
break;
}
}
return $pos;
}

/**
@param
@return


*/
public function skipRightWhitespace(int $pos) {
$tokens = $this->tokens;
for ($count = \count($tokens); $pos < $count; $pos++) {
$type = $tokens[$pos][0];
if ($type !== \T_WHITESPACE && $type !== \T_COMMENT && $type !== \T_DOC_COMMENT) {
break;
}
}
return $pos;
}

public function findRight(int $pos, $findTokenType) {
$tokens = $this->tokens;
for ($count = \count($tokens); $pos < $count; $pos++) {
$type = $tokens[$pos][0];
if ($type === $findTokenType) {
return $pos;
}
}
return -1;
}

/**
@param
@param
@param
@return


*/
public function haveTokenInRange(int $startPos, int $endPos, $tokenType) {
$tokens = $this->tokens;
for ($pos = $startPos; $pos < $endPos; $pos++) {
if ($tokens[$pos][0] === $tokenType) {
return true;
}
}
return false;
}

public function haveBracesInRange(int $startPos, int $endPos) {
return $this->haveTokenInRange($startPos, $endPos, '{')
|| $this->haveTokenInRange($startPos, $endPos, '}');
}

/**
@param
@return



*/
public function getIndentationBefore(int $pos) : int {
return $this->indentMap[$pos];
}

/**
@param
@param
@param
@return



*/
public function getTokenCode(int $from, int $to, int $indent) : string {
$tokens = $this->tokens;
$result = '';
for ($pos = $from; $pos < $to; $pos++) {
$token = $tokens[$pos];
if (\is_array($token)) {
$type = $token[0];
$content = $token[1];
if ($type === \T_CONSTANT_ENCAPSED_STRING || $type === \T_ENCAPSED_AND_WHITESPACE) {
$result .= $content;
} else {

 if ($indent < 0) {
$result .= str_replace("\n" . str_repeat(" ", -$indent), "\n", $content);
} elseif ($indent > 0) {
$result .= str_replace("\n", "\n" . str_repeat(" ", $indent), $content);
} else {
$result .= $content;
}
}
} else {
$result .= $token;
}
}
return $result;
}

/**
@return


*/
private function calcIndentMap() {
$indentMap = [];
$indent = 0;
foreach ($this->tokens as $token) {
$indentMap[] = $indent;

if ($token[0] === \T_WHITESPACE) {
$content = $token[1];
$newlinePos = \strrpos($content, "\n");
if (false !== $newlinePos) {
$indent = \strlen($content) - $newlinePos - 1;
}
}
}


 $indentMap[] = $indent;

return $indentMap;
}
}
