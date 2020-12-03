<?php










namespace Symfony\Component\Console\Event;

/**
@author


*/
class ConsoleCommandEvent extends ConsoleEvent
{



const RETURN_CODE_DISABLED = 113;




private $commandShouldRun = true;

/**
@return


*/
public function disableCommand()
{
return $this->commandShouldRun = false;
}

/**
@return


*/
public function enableCommand()
{
return $this->commandShouldRun = true;
}

/**
@return


*/
public function commandShouldRun()
{
return $this->commandShouldRun;
}
}
