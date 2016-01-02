<?php

namespace Undine\Cache;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Remove once doctrine-cache-bundle upgrades to doctrine-cache 1.6.
 */
class ApcuCache extends CacheProvider
{
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return apcu_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return apcu_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return apcu_store($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        // apcu_delete returns false if the id does not exist
        return apcu_delete($id) || ! apcu_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return apcu_clear_cache() && apcu_clear_cache('user');
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetchMultiple(array $keys)
    {
        return apcu_fetch($keys);
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $info = apcu_cache_info('', true);
        $sma  = apcu_sma_info();

        // @TODO - Temporary fix @see https://github.com/krakjoe/apcu/pull/42
        if (PHP_VERSION_ID >= 50500) {
            $info['num_hits']   = isset($info['num_hits'])   ? $info['num_hits']   : $info['nhits'];
            $info['num_misses'] = isset($info['num_misses']) ? $info['num_misses'] : $info['nmisses'];
            $info['start_time'] = isset($info['start_time']) ? $info['start_time'] : $info['stime'];
        }

        return array(
            Cache::STATS_HITS             => $info['num_hits'],
            Cache::STATS_MISSES           => $info['num_misses'],
            Cache::STATS_UPTIME           => $info['start_time'],
            Cache::STATS_MEMORY_USAGE     => $info['mem_size'],
            Cache::STATS_MEMORY_AVAILABLE => $sma['avail_mem'],
        );
    }
}