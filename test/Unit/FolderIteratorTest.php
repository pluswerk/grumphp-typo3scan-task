<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Test\Unit;

use PHPUnit\Framework\TestCase;
use Pluswerk\GrumPHPTypo3ScanTask\Collection\FolderCollection;
use Pluswerk\GrumPHPTypo3ScanTask\Collection\FolderIterator;
use SplFileInfo;

final class FolderIteratorTest extends TestCase
{
    /**
     * @test
     */
    public function current(): void
    {
        $iterator = $this->getIterator();

        $this->assertEquals(__DIR__ . '/Fixtures/FolderA', (string)$iterator->current());
    }

    /**
     * @test
     */
    public function next(): void
    {
        $iterator = $this->getIterator();
        $iterator->next();

        $this->assertEquals(__DIR__ . '/Fixtures/FolderB', (string)$iterator->current());
    }

    /**
     * @test
     */
    public function key(): void
    {
        $iterator = $this->getIterator();
        $iterator->next();
        $iterator->next();

        $this->assertSame(2, $iterator->key());
    }

    /**
     * @test
     */
    public function valid(): void
    {
        $iterator = $this->getIterator();

        $this->assertTrue($iterator->valid());

        $iterator->next();
        $iterator->next();

        $this->assertFalse($iterator->valid());
    }

    /**
     * @test
     */
    public function rewind(): void
    {
        $iterator = $this->getIterator();

        $iterator->next();
        $iterator->next();
        $iterator->rewind();

        $this->assertEquals(0, $iterator->key());
    }

    private function getIterator(): FolderIterator
    {
        return new FolderIterator($this->getCollection());
    }

    private function getCollection(): FolderCollection
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo(__DIR__ . '/Fixtures/FolderA/test-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/FolderB/test-file2.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file2);

        return $collection;
    }
}
