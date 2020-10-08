<?php

namespace TonyBogdanov\Memoize\Tests\Memoize;

use PHPUnit\Framework\TestCase;
use TonyBogdanov\Memoize\Memoize;
use TonyBogdanov\Memoize\Tests\Helper\TestClass1;

/**
 * Class PerformanceTest
 *
 * @package TonyBogdanov\Memoize\Tests\Memoize
 */
class PerformanceTest extends TestCase {

    public function work() {

        $v = 10000;
        while ( 1 < $v ) {

            $v -= 2;

        }

    }

    /**
     * @param int $iterations
     * @param callable ...$callbacks
     *
     * @return array
     */
    public function median( int $iterations, callable ... $callbacks ): array {

        $callbacksCount = count( $callbacks );
        $result = array_fill( 0, $callbacksCount, 0 );

        for ( $i = 0; $i < $iterations; $i++ ) {

            for ( $j = 0; $j < $callbacksCount; $j++ ) {

                $result[ $j ] += call_user_func( $callbacks[ $j ] );

            }

        }

        return $result;

    }

    /**
     * @return float
     */
    public function loopNative(): float {

        $start = microtime( true );

        for ( $i = 0; $i < 100; $i++ ) {

            call_user_func( [ $this, 'work' ] );

        }

        return microtime( true ) - $start;

    }

    /**
     * @param bool $purge
     *
     * @return float
     */
    public function loopClass( bool $purge = false ): float {

        $start = microtime( true );

        for ( $i = 0; $i < 100; $i++ ) {

            Memoize::memoize( __CLASS__, __METHOD__, [ $this, 'work' ] );
            if ( $purge ) {

                Memoize::unmemoize( __CLASS__, __METHOD__ );

            }

        }

        return microtime( true ) - $start;

    }

    /**
     * @return float
     */
    public function loopClassPurge(): float {

        return $this->loopClass( true );

    }

    /**
     * @param bool $purge
     *
     * @return float
     */
    public function loopObject( bool $purge = false ): float {

        $start = microtime( true );

        for ( $i = 0; $i < 100; $i++ ) {

            Memoize::memoize( $this, __METHOD__, [ $this, 'work' ] );
            if ( $purge ) {

                Memoize::unmemoize( $this, __METHOD__ );

            }

        }

        return microtime( true ) - $start;

    }

    /**
     * @return float
     */
    public function loopObjectPurge(): float {

        return $this->loopObject( true );

    }

    public function testCPU1() {

        [ $native, $class, $classPurge, $object, $objectPurge ] = $this->median(

            5,
            [ $this, 'loopNative' ],
            [ $this, 'loopClass' ],
            [ $this, 'loopClassPurge' ],
            [ $this, 'loopObject' ],
            [ $this, 'loopObjectPurge' ]

        );

        // Memoized is at least x10 faster than native.
        $this->assertGreaterThan( $class, $native / 10 );
        $this->assertGreaterThan( $object, $native / 10 );

        // Native is faster than memoized purged, but no more than 25%.
        $this->assertGreaterThan( $native, $classPurge );
        $this->assertGreaterThan( $native, $objectPurge );

        $this->assertGreaterThan( $classPurge, $native * 1.25 );
        $this->assertGreaterThan( $objectPurge, $native * 1.25 );

    }

    public function testCPU2() {

        [ $class, $object ] = $this->median(

            1000,
            [ $this, 'loopClass' ],
            [ $this, 'loopObject' ]

        );

        // Memoized do not differ more than 25% of each other.
        $this->assertGreaterThan( max( $class, $object ) / min( $class, $object ), 1.25 );

    }

    public function testMemoryOverhead() {

        $provider = function (): array {

            return array_map( function () {

                return mt_rand( 0, 1024 );

            }, array_fill( 0, 1024, 0 ) );

        };

        $bag = [];
        $start = memory_get_usage( true );

        for ( $i = 0; $i < 20000; $i++ ) {

            $bag[] = call_user_func( $provider );

        }

        $reference = memory_get_usage( true ) - $start;
        $start = memory_get_usage( true );

        for ( $i = 0; $i < 20000; $i++ ) {

            $bag[] = Memoize::memoize( TestClass1::class, 'a' . $i, $provider );

        }

        // Overhead remains under 10%.
        $this->assertGreaterThan( ( memory_get_usage( true ) - $start - $reference ) / $reference, 0.1 );

    }

}
