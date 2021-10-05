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
     * @return bool
     */
    protected static function isMemoizedStatic( string $key ): bool {
        return Memoize::isMemoized( static::class, $key );
    }

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
    protected static function unmemoizeStatic( string $key ): void {
        Memoize::unmemoize( static::class, $key );
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isMemoized( string $key ): bool {
        return Memoize::isMemoized( $this, $key );
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
     * @param string|null $key
     *
     * @return $this
     */
    protected function unmemoize( string $key = null ): self {
        Memoize::unmemoize( $this, $key );
        return $this;
    }

}
