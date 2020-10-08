<?php

namespace TonyBogdanov\Memoize;

use SplObjectStorage;
use TonyBogdanov\Memoize\Exceptions\UnknownOwnerException;
use WeakReference;

/**
 * Class Memoize
 *
 * @package TonyBogdanov\Memoize
 */
final class Memoize {

    /**
     * @var SplObjectStorage[]
     */
    private static array $storage = [];

    /**
     * @param $owner
     *
     * @return array
     */
    private static function owner( $owner ): array {

        if ( is_object( $owner ) ) {

            return [ get_class( $owner ), WeakReference::create( $owner ) ];

        }

        if ( is_string( $owner ) && class_exists( $owner ) ) {

            return [ $owner, VoidMarker::get() ];

        }

        throw new UnknownOwnerException( $owner );

    }

    /**
     * @param $owner
     * @param string $key
     * @param callable $provider
     *
     * @return mixed
     */
    public static function memoize( $owner, string $key, callable $provider ) {

        [ $group, $ref ] = static::owner( $owner );

        if ( ! isset( static::$storage[ $group ] ) ) {

            static::$storage[ $group ] = new SplObjectStorage();

        }

        if ( ! isset( static::$storage[ $group ][ $ref ] ) ) {

            static::$storage[ $group ][ $ref ] = [];

        }

        $data = static::$storage[ $group ][ $ref ];
        if ( ! array_key_exists( $key, $data ) ) {

            $data[ $key ] = call_user_func( $provider );
            static::$storage[ $group ][ $ref ] = $data;

        }

        return $data[ $key ];

    }

    /**
     * @param null $owner
     * @param string|null $key
     */
    public static function unmemoize( $owner = null, string $key = null ) {

        if ( ! isset( $owner ) ) {

            static::$storage = [];
            return;

        }

        [ $group, $ref ] = static::owner( $owner );

        if ( ! isset( static::$storage[ $group ] ) || ! isset( static::$storage[ $group ][ $ref ] ) ) {

            return;

        }

        if ( ! isset( $key ) ) {

            unset( static::$storage[ $group ][ $ref ] );
            return;

        }

        if ( ! array_key_exists( $key, static::$storage[ $group ][ $ref ] ) ) {

            return;

        }

        $data = static::$storage[ $group ][ $ref ];
        unset( $data[ $key ] );

        static::$storage[ $group ][ $ref ] = $data;

    }

}
