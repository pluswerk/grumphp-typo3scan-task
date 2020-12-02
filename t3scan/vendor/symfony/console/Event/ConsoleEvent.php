<?php










namespace Symfony\Component\Console\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
@author


*/
class ConsoleEvent extends Event
{
protected $command;

private $input;
private $output;

public function __construct(Command $command = null, InputInterface $input, OutputInterface $output)
{
$this->command = $command;
$this->input = $input;
$this->output = $output;
}

/**
@return


*/
public function getCommand()
{
return $this->command;
}

/**
@return


*/
public function getInput()
{
return $this->input;
}

/**
@return


*/
public function getOutput()
{
return $this->output;
}
}
