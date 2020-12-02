<?php










namespace Symfony\Component\Console\Output;

/**
@author
*/
class BufferedOutput extends Output
{
private $buffer = '';

/**
@return


*/
public function fetch()
{
$content = $this->buffer;
$this->buffer = '';

return $content;
}




protected function doWrite($message, $newline)
{
$this->buffer .= $message;

if ($newline) {
$this->buffer .= \PHP_EOL;
}
}
}
