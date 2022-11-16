<?php

namespace TonyBogdanov\Memoize\Exceptions;

use RuntimeException;

/**
 * Class UnknownOwnerException
 *
 * @package TonyBogdanov\Memoize\Exceptions
 *
 * @codeCoverageIgnore
 */
class UnknownOwnerException extends RuntimeException {

    /**
     * UnknownOwnerException constructor.
     *
     * @param $owner
     */
    public function __construct( $owner ) {
        if ( is_object( $owner ) ) {
            $owner = get_class( $owner );
        } else if ( is_array( $owner ) ) {
            $owner = '(array) ...';
        } else {
            $owner = var_export( $owner, true );
        }

        parent::__construct( sprintf( 'Unknown owner reference: %1$s.', $owner ) );
    }

}
