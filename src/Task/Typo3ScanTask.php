<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Task;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Pluswerk\GrumPHPTypo3ScanTask\Collection\Folder;
use Pluswerk\GrumPHPTypo3ScanTask\Collection\FolderCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Typo3ScanTask extends AbstractExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'extension_paths' => [],
            'target_version' => null,
            'type_of_changes' => ['breaking','deprecation','feature','important'],
            'indicators' => ['strong','weak']
        ]);

        $resolver->setAllowedValues('target_version', function ($value) {
            if ($value === null) {
                return true;
            }
            return false !== filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        });

        $resolver->addAllowedTypes('extension_paths', ['array']);
        $resolver->addAllowedTypes('type_of_changes', ['array']);
        $resolver->addAllowedTypes('indicators', ['array']);

        return $resolver;
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof RunContext || $context instanceof GitPreCommitContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $config = $this->getConfig()->getOptions();

        if (empty($config['extension_paths'])) {
            return TaskResult::createNonBlockingFailed(
                $this,
                $context,
                'You have to give at least one path in extension_paths!'
            );
        }

        $files = $context->getFiles()->paths($config['extension_paths']);

        if (0 === count($files)) {
            return TaskResult::createSkipped($this, $context);
        }

        $folders = new FolderCollection();
        foreach ($files as $folder) {
            $folders->addFileFolder($folder);
        }

        $message = '';
        $processFailed = false;

        /** @var Folder $folder */
        foreach ($folders as $folder) {
            $arguments = $this->processBuilder->createArgumentsForCommand('typo3scan');

            $arguments->add('scan');
            $arguments->addOptionalArgumentWithSeparatedValue('--only', implode(',', $config['type_of_changes']));
            $arguments->addOptionalArgumentWithSeparatedValue('--indicators', implode(',', $config['indicators']));
            $arguments->addOptionalArgumentWithSeparatedValue('--target', $config['target_version']);
            $arguments->add((string)$folder);

            $process = $this->processBuilder->buildProcess($arguments);

            $process->run();

            if (!$process->isSuccessful()) {
                $message .= PHP_EOL . PHP_EOL . '===============================================================' . PHP_EOL;
                $message .= $process->getCommandLine() . PHP_EOL;
                $message .= 'Scanned the following path:' . PHP_EOL;
                $message .= $this->formatter->format($process) . PHP_EOL;
                $processFailed = true;
            }
        }

        if ($processFailed) {
            return TaskResult::createFailed($this, $context, $message);
        }

        return TaskResult::createPassed($this, $context);
    }
}
