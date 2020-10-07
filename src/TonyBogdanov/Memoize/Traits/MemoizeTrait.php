<?php

namespace TonyBogdanov\Memoize\Traits;

/**
 * Provides useful methods for wrapping heavy functionality in getters on the object and class levels respectively.
 *
 * Pass a unique $key (usually __METHOD__) as first argument and a callable returning a value as second argument. The
 * callable will be invoked once and then the result will be cached for the specified key and returned without
 * invoking the callable upon further calls.
 *
 * Trait Memoize
 *
 * @package TonyBogdanov\Memoize\Traits
 */
trait MemoizeTrait {

    /**
     * @var mixed[]
     */
    protected static $__MEMOIDS_STATIC__ = [];

    /**
     * @var mixed[]
     */
    protected $__MEMOIDS__ = [];

    /**
     * @param string $key
     * @param callable $provider
     *
     * @return mixed
     */
    protected static function memoizeStatic( string $key, callable $provider ) {

        if ( ! array_key_exists( $key, static::$__MEMOIDS_STATIC__ ) ) {

            static::$__MEMOIDS_STATIC__[ $key ] = call_user_func( $provider );

        }

        return static::$__MEMOIDS_STATIC__[ $key ];

    }

    /**
     * @param string $key
     */
    protected static function dememoizeStatic( string $key ) {

        if ( array_key_exists( $key, static::$__MEMOIDS_STATIC__ ) ) {

            unset( static::$__MEMOIDS_STATIC__[ $key ] );

        }

    }

    protected static function purgeMemoidsStatic() {

        static::$__MEMOIDS_STATIC__ = [];

    }

    /**
     * @param string $key
     * @param callable $provider
     *
     * @return mixed
     */
    protected function memoize( string $key, callable $provider ) {

        if ( ! array_key_exists( $key, $this->__MEMOIDS__ ) ) {

            $this->__MEMOIDS__[ $key ] = call_user_func( $provider );

        }

        return $this->__MEMOIDS__[ $key ];

    }

    /**
     * @param string $key
     *
     * @return $this
     */
    protected function dememoize( string $key ): self {

        if ( array_key_exists( $key, $this->__MEMOIDS__ ) ) {

            unset( $this->__MEMOIDS__[ $key ] );

        }

        return $this;

    }

    /**
     * @return $this
     */
    protected function purgeMemoids(): self {

        $this->__MEMOIDS__ = [];
        return $this;

    }

}
