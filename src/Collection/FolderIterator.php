<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Collection;

final class FolderIterator implements \Iterator
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var FolderCollection
     */
    private $folderCollection;

    /**
     * FolderIterator constructor.
     * @param FolderCollection $folderCollection
     */
    public function __construct(FolderCollection $folderCollection)
    {
        $this->folderCollection = $folderCollection;
    }

    /**
     * @inheritDoc
     */
    public function current(): Folder
    {
        return $this->folderCollection->getFolder($this->position);
    }

    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->position++;
    }

    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return !is_null($this->folderCollection->getFolder($this->position));
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
}
