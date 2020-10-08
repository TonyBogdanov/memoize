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

        parent::__construct( sprintf(

            'Unknown owner reference: %1$s.',
            is_object( $owner ) ?
                get_class( $owner ) :
                ( is_array( $owner ) ? '(array) ...' : var_export( $owner, true ) )

        ) );

    }

}
