<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
@author


*/
abstract class InputAwareHelper extends Helper implements InputAwareInterface
{
protected $input;




public function setInput(InputInterface $input)
{
$this->input = $input;
}
}
