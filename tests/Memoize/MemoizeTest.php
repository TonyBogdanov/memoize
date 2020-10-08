<?php

namespace TonyBogdanov\Memoize\Tests\Memoize;

use PHPUnit\Framework\TestCase;
use TonyBogdanov\Memoize\Exceptions\UnknownOwnerException;
use TonyBogdanov\Memoize\Memoize;
use TonyBogdanov\Memoize\Tests\Helper\State;
use TonyBogdanov\Memoize\Tests\Helper\TestClass1;

/**
 * Class MemoizeTest
 *
 * @package TonyBogdanov\Memoize\Tests\Memoize
 *
 * @runTestsInSeparateProcesses
 */
class MemoizeTest extends TestCase {

    /**
     * @return array
     */
    public function matrixSimple(): array {

        return [

            [ 'class', new State() ],
            [ 'object', new State() ],

        ];

    }

    /**
     * @return array
     */
    public function matrixAdvanced(): array {

        return [

            [ 'class', 'first', new State() ],
            [ 'class', 'second', new State() ],
            [ 'object', 'first', new State() ],
            [ 'object', 'second', new State() ],

        ];

    }

    /**
     * @return array
     */
    public function matrixComplete(): array {

        return [

            [ 'class', 'first', 'a', new State() ],
            [ 'class', 'first', 'b', new State() ],
            [ 'class', 'second', 'a', new State() ],
            [ 'class', 'second', 'b', new State() ],
            [ 'object', 'first', 'a', new State() ],
            [ 'object', 'first', 'b', new State() ],
            [ 'object', 'second', 'a', new State() ],
            [ 'object', 'second', 'b', new State() ],

        ];

    }

    /**
     * @dataProvider matrixComplete
     *
     * @param string $group
     * @param string $instance
     * @param string $key
     * @param State $state
     */
    public function testMemoize( string $group, string $instance, string $key, State $state ) {

        $owner = $state->owners[ $group ][ $instance ][ $key ];
        $provider = $state->providers[ $group ][ $instance ][ $key ];

        $value = Memoize::memoize( $owner, $key, $provider );
        $this->assertSame( $value, $provider->getValue() );

        $cached = Memoize::memoize( $owner, $key, $provider );
        $this->assertSame( $cached, $provider->getValue() );

        $state->invoked[ $group ][ $instance ][ $key ] = true;
        $state->assert( $this );

    }

    /**
     * @dataProvider matrixComplete
     *
     * @param string $group
     * @param string $instance
     * @param string $key
     * @param State $state
     */
    public function testUnmemoizeWithKey( string $group, string $instance, string $key, State $state ) {

        $owner = $state->owners[ $group ][ $instance ][ $key ];
        $provider = $state->providers[ $group ][ $instance ][ $key ];

        $initial = Memoize::memoize( $owner, $key, $provider );
        Memoize::unmemoize( $owner, $key );

        $reInvoked = Memoize::memoize( $owner, $key, $provider );
        $this->assertSame( $initial, $reInvoked );

        $state->invoked[ $group ][ $instance ][ $key ] = true;
        $state->reInvoked[ $group ][ $instance ][ $key ] = true;
        $state->assert( $this );

    }

    /**
     * @dataProvider matrixAdvanced
     *
     * @param string $group
     * @param string $instance
     * @param State $state
     */
    public function testUnmemoizeWithoutKey( string $group, string $instance, State $state ) {

        $owner = $state->owners[ $group ][ $instance ]['a']; // same owner for both a and b.

        $providerA = $state->providers[ $group ][ $instance ]['a'];
        $providerB = $state->providers[ $group ][ $instance ]['b'];

        $initialA = Memoize::memoize( $owner, 'a', $providerA );
        $initialB = Memoize::memoize( $owner, 'b', $providerB );

        Memoize::unmemoize( $owner );

        $reInvokedA = Memoize::memoize( $owner, 'a', $providerA );
        $reInvokedB = Memoize::memoize( $owner, 'b', $providerB );

        $this->assertSame( $initialA, $reInvokedA );
        $this->assertSame( $initialB, $reInvokedB );

        $state->invoked[ $group ][ $instance ]['a'] = true;
        $state->invoked[ $group ][ $instance ]['b'] = true;

        $state->reInvoked[ $group ][ $instance ]['a'] = true;
        $state->reInvoked[ $group ][ $instance ]['b'] = true;

        $state->assert( $this );

    }

    /**
     * @dataProvider matrixSimple
     *
     * @param string $group
     * @param State $state
     */
    public function testUnmemoizeWithoutOwner( string $group, State $state ) {

        $ownerFirst = $state->owners[ $group ]['first']['a']; // same owner for both a and b.
        $ownerSecond = $state->owners[ $group ]['second']['a']; // same owner for both a and b.

        $providerFirstA = $state->providers[ $group ]['first']['a'];
        $providerSecondA = $state->providers[ $group ]['second']['a'];

        $providerFirstB = $state->providers[ $group ]['first']['b'];
        $providerSecondB = $state->providers[ $group ]['second']['b'];

        $initialFirstA = Memoize::memoize( $ownerFirst, 'a', $providerFirstA );
        $initialSecondA = Memoize::memoize( $ownerSecond, 'a', $providerSecondA );

        $initialFirstB = Memoize::memoize( $ownerFirst, 'b', $providerFirstB );
        $initialSecondB = Memoize::memoize( $ownerSecond, 'b', $providerSecondB );

        Memoize::unmemoize();

        $reInvokedFirstA = Memoize::memoize( $ownerFirst, 'a', $providerFirstA );
        $reInvokedSecondA = Memoize::memoize( $ownerSecond, 'a', $providerSecondA );

        $reInvokedFirstB = Memoize::memoize( $ownerFirst, 'b', $providerFirstB );
        $reInvokedSecondB = Memoize::memoize( $ownerSecond, 'b', $providerSecondB );

        $this->assertSame( $initialFirstA, $reInvokedFirstA );
        $this->assertSame( $initialSecondA, $reInvokedSecondA );

        $this->assertSame( $initialFirstB, $reInvokedFirstB );
        $this->assertSame( $initialSecondB, $reInvokedSecondB );

        $state->invoked[ $group ]['first']['a'] = true;
        $state->invoked[ $group ]['second']['a'] = true;

        $state->invoked[ $group ]['first']['b'] = true;
        $state->invoked[ $group ]['second']['b'] = true;

        $state->reInvoked[ $group ]['first']['a'] = true;
        $state->reInvoked[ $group ]['second']['a'] = true;

        $state->reInvoked[ $group ]['first']['b'] = true;
        $state->reInvoked[ $group ]['second']['b'] = true;

        $state->assert( $this );

    }

    public function testUnmemoizeUnknownKey() {

        Memoize::memoize( TestClass1::class, 'a', function () {} );
        Memoize::unmemoize( TestClass1::class, '123' );

        $this->assertTrue( true ); // just assert no errors were thrown

    }

    public function testUnmemoizeUnknownOwner() {

        Memoize::unmemoize( TestClass1::class );

        $this->assertTrue( true ); // just assert no errors were thrown

    }

    public function testUnknownOwner() {

        $this->expectException( UnknownOwnerException::class );
        Memoize::memoize( '123', 'a', function () {} );

    }

}
