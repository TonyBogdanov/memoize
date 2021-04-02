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
     * @var bool
     */
    private static bool $enabled = true;

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

    public static function enable() {

        self::$enabled = true;

    }

    public static function disable() {

        self::$enabled = false;

    }

    /**
     * @param $owner
     * @param string $key
     * @param callable $provider
     *
     * @return mixed
     */
    public static function memoize( $owner, string $key, callable $provider ) {

        if ( ! self::$enabled ) {

            return call_user_func( $provider );

        }

        [ $group, $ref ] = self::owner( $owner );

        if ( ! isset( self::$storage[ $group ] ) ) {

            self::$storage[ $group ] = new SplObjectStorage();

        }

        if ( ! isset( self::$storage[ $group ][ $ref ] ) ) {

            self::$storage[ $group ][ $ref ] = [];

        }

        $data = self::$storage[ $group ][ $ref ];
        if ( ! array_key_exists( $key, $data ) ) {

            $data[ $key ] = call_user_func( $provider );
            self::$storage[ $group ][ $ref ] = $data;

        }

        return $data[ $key ];

    }

    /**
     * @param null $owner
     * @param string|null $key
     */
    public static function unmemoize( $owner = null, string $key = null ) {

        if ( ! isset( $owner ) ) {

            self::$storage = [];
            return;

        }

        [ $group, $ref ] = self::owner( $owner );

        if ( ! isset( self::$storage[ $group ] ) || ! isset( self::$storage[ $group ][ $ref ] ) ) {

            return;

        }

        if ( ! isset( $key ) ) {

            unset( self::$storage[ $group ][ $ref ] );
            return;

        }

        if ( ! array_key_exists( $key, self::$storage[ $group ][ $ref ] ) ) {

            return;

        }

        $data = self::$storage[ $group ][ $ref ];
        unset( $data[ $key ] );

        self::$storage[ $group ][ $ref ] = $data;

    }

}
