<?php










namespace Symfony\Component\Console\Helper;

/**
@author




*/
class DebugFormatterHelper extends Helper
{
private $colors = ['black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white', 'default'];
private $started = [];
private $count = -1;

/**
@param
@param
@param
@return



*/
public function start($id, $message, $prefix = 'RUN')
{
$this->started[$id] = ['border' => ++$this->count % \count($this->colors)];

return sprintf("%s<bg=blue;fg=white> %s </> <fg=blue>%s</>\n", $this->getBorder($id), $prefix, $message);
}

/**
@param
@param
@param
@param
@param
@return



*/
public function progress($id, $buffer, $error = false, $prefix = 'OUT', $errorPrefix = 'ERR')
{
$message = '';

if ($error) {
if (isset($this->started[$id]['out'])) {
$message .= "\n";
unset($this->started[$id]['out']);
}
if (!isset($this->started[$id]['err'])) {
$message .= sprintf('%s<bg=red;fg=white> %s </> ', $this->getBorder($id), $errorPrefix);
$this->started[$id]['err'] = true;
}

$message .= str_replace("\n", sprintf("\n%s<bg=red;fg=white> %s </> ", $this->getBorder($id), $errorPrefix), $buffer);
} else {
if (isset($this->started[$id]['err'])) {
$message .= "\n";
unset($this->started[$id]['err']);
}
if (!isset($this->started[$id]['out'])) {
$message .= sprintf('%s<bg=green;fg=white> %s </> ', $this->getBorder($id), $prefix);
$this->started[$id]['out'] = true;
}

$message .= str_replace("\n", sprintf("\n%s<bg=green;fg=white> %s </> ", $this->getBorder($id), $prefix), $buffer);
}

return $message;
}

/**
@param
@param
@param
@param
@return



*/
public function stop($id, $message, $successful, $prefix = 'RES')
{
$trailingEOL = isset($this->started[$id]['out']) || isset($this->started[$id]['err']) ? "\n" : '';

if ($successful) {
return sprintf("%s%s<bg=green;fg=white> %s </> <fg=green>%s</>\n", $trailingEOL, $this->getBorder($id), $prefix, $message);
}

$message = sprintf("%s%s<bg=red;fg=white> %s </> <fg=red>%s</>\n", $trailingEOL, $this->getBorder($id), $prefix, $message);

unset($this->started[$id]['out'], $this->started[$id]['err']);

return $message;
}

/**
@param
@return

*/
private function getBorder($id)
{
return sprintf('<bg=%s> </>', $this->colors[$this->started[$id]['border']]);
}




public function getName()
{
return 'debug_formatter';
}
}
