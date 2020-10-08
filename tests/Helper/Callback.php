<?php

namespace TonyBogdanov\Memoize\Tests\Helper;

/**
 * Class Callback
 *
 * @package TonyBogdanov\Memoize\Tests\Helper
 */
class Callback {

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int
     */
    protected int $invocations = 0;

    /**
     * Callback constructor.
     *
     * @param mixed $value
     */
    public function __construct( $value ) {

        $this->value = $value;

    }

    /**
     * @return mixed
     */
    public function __invoke() {

        $this->invocations++;
        return $this->value;

    }

    /**
     * @return mixed
     */
    public function getValue() {

        return $this->value;

    }

    /**
     * @return int
     */
    public function getInvocations(): int {

        return $this->invocations;

    }

}
