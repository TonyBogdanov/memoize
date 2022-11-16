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
     * @param string $foreignClass
     * @param string $key
     * @return bool
     */
    protected static function isMemoizedStaticForeign( string $foreignClass, string $key ): bool {
        return Memoize::isMemoized( $foreignClass, $key );
    }

    /**
     * @param string $key
     * @param callable $provider
     * @return mixed
     */
    protected static function memoizeStatic( string $key, callable $provider ) {
        return Memoize::memoize( static::class, $key, $provider );
    }

    /**
     * @param string $foreignClass
     * @param string $key
     * @param callable $provider
     * @return mixed
     */
    protected static function memoizeStaticForeign( string $foreignClass, string $key, callable $provider ) {
        return Memoize::memoize( $foreignClass, $key, $provider );
    }

    /**
     * @param string $key
     * @return void
     */
    protected static function unmemoizeStatic( string $key ): void {
        Memoize::unmemoize( static::class, $key );
    }

    /**
     * @param string $foreignClass
     * @param string $key
     * @return void
     */
    protected static function unmemoizeStaticForeign( string $foreignClass, string $key ): void {
        Memoize::unmemoize( $foreignClass, $key );
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isMemoized( string $key ): bool {
        return Memoize::isMemoized( $this, $key );
    }

    /**
     * @param object $foreignObject
     * @param string $key
     * @return bool
     */
    protected function isMemoizedForeign( object $foreignObject, string $key ): bool {
        return Memoize::isMemoized( $foreignObject, $key );
    }

    /**
     * @param string $key
     * @param callable $provider
     * @return mixed
     */
    protected function memoize( string $key, callable $provider ) {
        return Memoize::memoize( $this, $key, $provider );
    }

    /**
     * @param object $foreignObject
     * @param string $key
     * @param callable $provider
     * @return mixed
     */
    protected function memoizeForeign( object $foreignObject, string $key, callable $provider ) {
        return Memoize::memoize( $foreignObject, $key, $provider );
    }

    /**
     * @param string|null $key
     * @return $this
     */
    protected function unmemoize( string $key = null ): self {
        Memoize::unmemoize( $this, $key );
        return $this;
    }

    /**
     * @param object $foreignObject
     * @param string|null $key
     * @return $this
     */
    protected function unmemoizeForeign( object $foreignObject, string $key = null ): self {
        Memoize::unmemoize( $foreignObject, $key );
        return $this;
    }

}
