<?php










namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Output\OutputInterface;

/**
@author


*/
interface DescriptorInterface
{
/**
@param


*/
public function describe(OutputInterface $output, $object, array $options = []);
}
