<?php










namespace Symfony\Component\Console\Output;

/**
@author



*/
interface ConsoleOutputInterface extends OutputInterface
{
/**
@return


*/
public function getErrorOutput();

public function setErrorOutput(OutputInterface $error);
}
