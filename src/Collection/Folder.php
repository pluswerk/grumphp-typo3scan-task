<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Collection;

final class Folder
{
    /**
     * @var string
     */
    private $path;

    /**
     * Folder constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->path;
    }
}
