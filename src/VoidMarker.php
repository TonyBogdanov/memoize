<?php

namespace TonyBogdanov\Memoize;

/**
 * Class Marker
 *
 * @package TonyBogdanov\Memoize
 */
final class VoidMarker {

    /** @var VoidMarker */
    private static VoidMarker $reference;

    /**
     * @return VoidMarker
     */
    public static function get(): VoidMarker {

        if ( ! isset( self::$reference ) ) {
            self::$reference = new self();
        }

        return self::$reference;

    }

}
