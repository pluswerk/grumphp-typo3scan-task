<?php










namespace Symfony\Component\Debug\Exception;

/**
@author


*/
class SilencedErrorContext implements \JsonSerializable
{
public $count = 1;

private $severity;
private $file;
private $line;
private $trace;

public function __construct($severity, $file, $line, array $trace = [], $count = 1)
{
$this->severity = $severity;
$this->file = $file;
$this->line = $line;
$this->trace = $trace;
$this->count = $count;
}

public function getSeverity()
{
return $this->severity;
}

public function getFile()
{
return $this->file;
}

public function getLine()
{
return $this->line;
}

public function getTrace()
{
return $this->trace;
}

public function jsonSerialize()
{
return [
'severity' => $this->severity,
'file' => $this->file,
'line' => $this->line,
'trace' => $this->trace,
'count' => $this->count,
];
}
}
