<?php










namespace Symfony\Component\Console;

/**
@author


*/
final class ConsoleEvents
{
/**
@Event("Symfony\Component\Console\Event\ConsoleCommandEvent")




*/
const COMMAND = 'console.command';

/**
@Event("Symfony\Component\Console\Event\ConsoleTerminateEvent")



*/
const TERMINATE = 'console.terminate';

/**
@Event("Symfony\Component\Console\Event\ConsoleExceptionEvent")
@deprecated







*/
const EXCEPTION = 'console.exception';

/**
@Event("Symfony\Component\Console\Event\ConsoleErrorEvent")





*/
const ERROR = 'console.error';
}
