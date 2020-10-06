<?php

declare(strict_types=1);

namespace Pluswerk\GrumPHPTypo3ScanTask\Test\Unit;

use PHPUnit\Framework\TestCase;
use Pluswerk\GrumPHPTypo3ScanTask\Collection\FolderCollection;
use Pluswerk\GrumPHPTypo3ScanTask\Collection\FolderIterator;
use SplFileInfo;

final class FolderCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function ifNoFolderIsPresentNullIsReturned(): void
    {
        $collection = new FolderCollection();

        $this->assertNull($collection->getFolder(0));
        $this->assertSame(0, $collection->count());
    }

    /**
     * @test
     */
    public function aFolderCanBeAddedFromFileObject(): void
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo(__DIR__ . '/Fixtures/FolderA/test-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/FolderB/test-file2.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file2);

        $this->assertSame($file1->getPath(), (string)$collection->getFolder(0));
        $this->assertSame($file2->getPath(), (string)$collection->getFolder(1));
    }

    /**
     * @test
     */
    public function iteratorIsReturnedFromCollection(): void
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo(__DIR__ . '/Fixtures/FolderA/test-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/FolderB/test-file2.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file2);

        $expected = new FolderIterator($collection);

        $this->assertEquals($expected, $collection->getIterator());
    }

    /**
     * @test
     */
    public function aFolderPathIsUniqueInCollection(): void
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo(__DIR__ . '/Fixtures/FolderA/test-file.php');
        $file1A = new SplFileInfo(__DIR__ . '/Fixtures/FolderA/second-test-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/FolderB/test-file2.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file1A);
        $collection->addFileFolder($file2);

        $this->assertSame($file1->getPath(), (string)$collection->getFolder(0));
        $this->assertSame($file2->getPath(), (string)$collection->getFolder(1));
        $this->assertSame(2, $collection->count());
    }

    /**
     * @test
     */
    public function ifParentFolderIsAlreadyInCollectionAFolderIsNotAdded(): void
    {
        $collection = new FolderCollection();

        $file1A = new SplFileInfo(__DIR__ . '/Fixtures/parent-folder-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/FolderB/test-file2.php');

        $collection->addFileFolder($file1A);
        $collection->addFileFolder($file2);

        $this->assertSame($file1A->getPath(), (string)$collection->getFolder(0));
        $this->assertSame(1, $collection->count());
    }

    /**
     * @test
     */
    public function aFolderIsRemovedIfParentFolderIsAdded(): void
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo(__DIR__ . '/Fixtures/FolderA/test-file.php');
        $file1A = new SplFileInfo(__DIR__ . '/Fixtures/parent-folder-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/FolderB/test-file2.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file1A);
        $collection->addFileFolder($file2);

        $this->assertSame($file1A->getPath(), (string)$collection->getFolder(0));
        $this->assertSame(1, $collection->count());
    }

    /**
     * @test
     */
    public function folderWithSameStartSubStringAreBothInCollection(): void
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo(__DIR__ . '/Fixtures/extension/test-file.php');
        $file2 = new SplFileInfo(__DIR__ . '/Fixtures/extension_two/test-file2.php');
        $file3 = new SplFileInfo(__DIR__ . '/Fixtures/extension/second-test-file.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file2);
        $collection->addFileFolder($file3);

        $this->assertSame(2, $collection->count());
        $this->assertSame($file1->getPath(), (string)$collection->getFolder(0));
        $this->assertSame($file2->getPath(), (string)$collection->getFolder(1));
    }

    /**
     * @test
     */
    public function collectionWorksAlsoWithRelativePaths(): void
    {
        $collection = new FolderCollection();

        $file1 = new SplFileInfo('Fixtures/FolderA/test-file.php');
        $file2 = new SplFileInfo('same-folder-file.php');

        $collection->addFileFolder($file1);
        $collection->addFileFolder($file2);

        $this->assertSame($file1->getPath(), (string)$collection->getFolder(0));
        $this->assertSame('.', (string)$collection->getFolder(1));
    }
}
