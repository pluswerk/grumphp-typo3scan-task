<?php










namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\RuntimeException;

/**
@author


*/
class InputStream implements \IteratorAggregate
{
/**
@var */
private $onEmpty = null;
private $input = [];
private $open = true;




public function onEmpty(callable $onEmpty = null)
{
$this->onEmpty = $onEmpty;
}

/**
@param



*/
public function write($input)
{
if (null === $input) {
return;
}
if ($this->isClosed()) {
throw new RuntimeException(sprintf('"%s" is closed.', static::class));
}
$this->input[] = ProcessUtils::validateInput(__METHOD__, $input);
}




public function close()
{
$this->open = false;
}




public function isClosed()
{
return !$this->open;
}

public function getIterator()
{
$this->open = true;

while ($this->open || $this->input) {
if (!$this->input) {
yield '';
continue;
}
$current = array_shift($this->input);

if ($current instanceof \Iterator) {
foreach ($current as $cur) {
yield $cur;
}
} else {
yield $current;
}
if (!$this->input && $this->open && null !== $onEmpty = $this->onEmpty) {
$this->write($onEmpty($this));
}
}
}
}
