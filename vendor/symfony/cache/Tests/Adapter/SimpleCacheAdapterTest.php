<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MolliePrefix\Symfony\Component\Cache\Tests\Adapter;

use MolliePrefix\Symfony\Component\Cache\Adapter\SimpleCacheAdapter;
use MolliePrefix\Symfony\Component\Cache\Simple\ArrayCache;
use MolliePrefix\Symfony\Component\Cache\Simple\FilesystemCache;
/**
 * @group time-sensitive
 */
class SimpleCacheAdapterTest extends \MolliePrefix\Symfony\Component\Cache\Tests\Adapter\AdapterTestCase
{
    protected $skippedTests = ['testPrune' => 'SimpleCache just proxies'];
    public function createCachePool($defaultLifetime = 0)
    {
        return new \MolliePrefix\Symfony\Component\Cache\Adapter\SimpleCacheAdapter(new \MolliePrefix\Symfony\Component\Cache\Simple\FilesystemCache(), '', $defaultLifetime);
    }
    public function testValidCacheKeyWithNamespace()
    {
        $cache = new \MolliePrefix\Symfony\Component\Cache\Adapter\SimpleCacheAdapter(new \MolliePrefix\Symfony\Component\Cache\Simple\ArrayCache(), 'some_namespace', 0);
        $item = $cache->getItem('my_key');
        $item->set('someValue');
        $cache->save($item);
        $this->assertTrue($cache->getItem('my_key')->isHit(), 'Stored item is successfully retrieved.');
    }
}
