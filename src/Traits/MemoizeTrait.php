<?php

namespace TonyBogdanov\Memoize\Traits;

use TonyBogdanov\Memoize\Memoize;

/**
 * Trait MemoizeTrait
 *
 * @package TonyBogdanov\Memoize\Traits
 *
 * @codeCoverageIgnore
 */
trait MemoizeTrait {

    /**
     * @param string $key
     * @param callable $provider
     *
     * @return mixed
     */
    protected static function memoizeStatic( string $key, callable $provider ) {

        return Memoize::memoize( static::class, $key, $provider );

    }

    /**
     * @param string $key
     */
    protected static function unmemoizeStatic( string $key ) {

        Memoize::unmemoize( static::class, $key );

    }

    /**
     * @param string $key
     * @param callable $provider
     *
     * @return mixed
     */
    protected function memoize( string $key, callable $provider ) {

        return Memoize::memoize( $this, $key, $provider );

    }

    /**
     * @param string $key
     *
     * @return $this
     */
    protected function unmemoize( string $key ): self {

        Memoize::unmemoize( $this, $key );
        return $this;

    }

}
