<?php declare(strict_types=1);

namespace PhpParser;

class Error extends \RuntimeException
{
protected $rawMessage;
protected $attributes;

/**
@param
@param



*/
public function __construct(string $message, $attributes = []) {
$this->rawMessage = $message;
if (is_array($attributes)) {
$this->attributes = $attributes;
} else {
$this->attributes = ['startLine' => $attributes];
}
$this->updateMessage();
}

/**
@return


*/
public function getRawMessage() : string {
return $this->rawMessage;
}

/**
@return


*/
public function getStartLine() : int {
return $this->attributes['startLine'] ?? -1;
}

/**
@return


*/
public function getEndLine() : int {
return $this->attributes['endLine'] ?? -1;
}

/**
@return


*/
public function getAttributes() : array {
return $this->attributes;
}

/**
@param


*/
public function setAttributes(array $attributes) {
$this->attributes = $attributes;
$this->updateMessage();
}

/**
@param


*/
public function setRawMessage(string $message) {
$this->rawMessage = $message;
$this->updateMessage();
}

/**
@param


*/
public function setStartLine(int $line) {
$this->attributes['startLine'] = $line;
$this->updateMessage();
}

/**
@return




*/
public function hasColumnInfo() : bool {
return isset($this->attributes['startFilePos'], $this->attributes['endFilePos']);
}

/**
@param
@return


*/
public function getStartColumn(string $code) : int {
if (!$this->hasColumnInfo()) {
throw new \RuntimeException('Error does not have column information');
}

return $this->toColumn($code, $this->attributes['startFilePos']);
}

/**
@param
@return


*/
public function getEndColumn(string $code) : int {
if (!$this->hasColumnInfo()) {
throw new \RuntimeException('Error does not have column information');
}

return $this->toColumn($code, $this->attributes['endFilePos']);
}

/**
@param
@return



*/
public function getMessageWithColumnInfo(string $code) : string {
return sprintf(
'%s from %d:%d to %d:%d', $this->getRawMessage(),
$this->getStartLine(), $this->getStartColumn($code),
$this->getEndLine(), $this->getEndColumn($code)
);
}

/**
@param
@param
@return



*/
private function toColumn(string $code, int $pos) : int {
if ($pos > strlen($code)) {
throw new \RuntimeException('Invalid position information');
}

$lineStartPos = strrpos($code, "\n", $pos - strlen($code));
if (false === $lineStartPos) {
$lineStartPos = -1;
}

return $pos - $lineStartPos;
}




protected function updateMessage() {
$this->message = $this->rawMessage;

if (-1 === $this->getStartLine()) {
$this->message .= ' on unknown line';
} else {
$this->message .= ' on line ' . $this->getStartLine();
}
}
}
