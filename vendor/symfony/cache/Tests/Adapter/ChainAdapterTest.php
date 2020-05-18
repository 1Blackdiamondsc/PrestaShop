<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Tests\Adapter;

use _PhpScoper5ea00cc67502b\PHPUnit\Framework\MockObject\MockObject;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Adapter\AdapterInterface;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Adapter\ArrayAdapter;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Adapter\ChainAdapter;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Adapter\FilesystemAdapter;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\PruneableInterface;
use _PhpScoper5ea00cc67502b\Symfony\Component\Cache\Tests\Fixtures\ExternalAdapter;
use stdClass;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 * @group time-sensitive
 */
class ChainAdapterTest extends AdapterTestCase
{
    public function createCachePool($defaultLifetime = 0)
    {
        return new ChainAdapter([new ArrayAdapter($defaultLifetime), new ExternalAdapter(), new FilesystemAdapter('', $defaultLifetime)], $defaultLifetime);
    }
    public function testEmptyAdaptersException()
    {
        $this->expectException('_PhpScoper5ea00cc67502b\\Symfony\\Component\\Cache\\Exception\\InvalidArgumentException');
        $this->expectExceptionMessage('At least one adapter must be specified.');
        new ChainAdapter([]);
    }
    public function testInvalidAdapterException()
    {
        $this->expectException('_PhpScoper5ea00cc67502b\\Symfony\\Component\\Cache\\Exception\\InvalidArgumentException');
        $this->expectExceptionMessage('The class "stdClass" does not implement');
        new ChainAdapter([new stdClass()]);
    }
    public function testPrune()
    {
        if (isset($this->skippedTests[__FUNCTION__])) {
            $this->markTestSkipped($this->skippedTests[__FUNCTION__]);
        }
        $cache = new ChainAdapter([$this->getPruneableMock(), $this->getNonPruneableMock(), $this->getPruneableMock()]);
        $this->assertTrue($cache->prune());
        $cache = new ChainAdapter([$this->getPruneableMock(), $this->getFailingPruneableMock(), $this->getPruneableMock()]);
        $this->assertFalse($cache->prune());
    }
    /**
     * @return MockObject|PruneableCacheInterface
     */
    private function getPruneableMock()
    {
        $pruneable = $this->getMockBuilder(PruneableCacheInterface::class)->getMock();
        $pruneable->expects($this->atLeastOnce())->method('prune')->willReturn(true);
        return $pruneable;
    }
    /**
     * @return MockObject|PruneableCacheInterface
     */
    private function getFailingPruneableMock()
    {
        $pruneable = $this->getMockBuilder(PruneableCacheInterface::class)->getMock();
        $pruneable->expects($this->atLeastOnce())->method('prune')->willReturn(false);
        return $pruneable;
    }
    /**
     * @return MockObject|AdapterInterface
     */
    private function getNonPruneableMock()
    {
        return $this->getMockBuilder(AdapterInterface::class)->getMock();
    }
}
interface PruneableCacheInterface extends PruneableInterface, AdapterInterface
{
}
