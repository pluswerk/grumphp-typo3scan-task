<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Collection;

use SplFileInfo;
use function count;

final class FolderCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array<Folder>
     */
    private $folders = [];

    public function getIterator()
    {
        return new FolderIterator($this);
    }

    public function count()
    {
        return count($this->folders);
    }

    public function getFolder(int $position): ?Folder
    {
        return $this->folders[$position] ?? null;
    }

    public function addFileFolder(SplFileInfo $fileInfo): void
    {
        $path = $this->buildPath($fileInfo);
        if ($this->filterDuplicateFolder($path) && $this->filterParentFolder($path)) {
            $this->removeSubFolders($path);
            $this->folders[] = new Folder($path);
        }
    }

    private function filterDuplicateFolder(string $path): bool
    {
        foreach ($this->folders as $folder) {
            if ((string)$folder === $path) {
                return false;
            }
        }

        return true;
    }

    private function filterParentFolder(string $path): bool
    {
        foreach ($this->folders as $folder) {
            $folderString = rtrim((string)$folder, '/') . '/';
            if (substr($path, 0, strlen($folderString)) === $folderString) {
                return false;
            }
        }

        return true;
    }

    private function removeSubFolders(string $path): void
    {
        foreach ($this->folders as $position => $folder) {
            if (substr((string)$folder, 0, strlen($path)) === $path) {
                unset($this->folders[$position]);
            }
        }

        $this->folders = array_values($this->folders);
    }

    private function buildPath(SplFileInfo $fileInfo): string
    {
        $path = $fileInfo->getPath();

        if ($fileInfo->getPathname() === $fileInfo->getFilename()) {
            $path = '.';
        }

        return $path;
    }
}
