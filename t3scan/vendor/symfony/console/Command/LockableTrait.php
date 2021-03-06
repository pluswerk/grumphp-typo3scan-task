<?php










namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Lock;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
@author


*/
trait LockableTrait
{
/**
@var */
private $lock;

/**
@return


*/
private function lock($name = null, $blocking = false)
{
if (!class_exists(SemaphoreStore::class)) {
throw new RuntimeException('To enable the locking feature you must install the symfony/lock component.');
}

if (null !== $this->lock) {
throw new LogicException('A lock is already in place.');
}

if (SemaphoreStore::isSupported($blocking)) {
$store = new SemaphoreStore();
} else {
$store = new FlockStore();
}

$this->lock = (new Factory($store))->createLock($name ?: $this->getName());
if (!$this->lock->acquire($blocking)) {
$this->lock = null;

return false;
}

return true;
}




private function release()
{
if ($this->lock) {
$this->lock->release();
$this->lock = null;
}
}
}
