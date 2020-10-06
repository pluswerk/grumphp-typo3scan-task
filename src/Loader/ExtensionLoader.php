<?php

namespace Pluswerk\GrumPHPTypo3ScanTask\Loader;

use GrumPHP\Extension\ExtensionInterface;
use Pluswerk\GrumPHPTypo3ScanTask\Task\Typo3ScanTask;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ExtensionLoader implements ExtensionInterface
{
    public function load(ContainerBuilder $container)
    {
        return $container->register('task.typo3scan', Typo3ScanTask::class)
            ->addArgument(new Reference('process_builder'))
            ->addArgument(new Reference('formatter.raw_process'))
            ->addTag('grumphp.task', ['task' => 'typo3scan']);
    }
}
