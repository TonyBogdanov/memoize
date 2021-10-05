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
     * @param bool $existsPrior
     * @param $owner
     * @param string $key
     * @param callable $provider
     * @return mixed
     */
    protected function doMemoize( bool $existsPrior, $owner, string $key, callable $provider ) {

        $existsPrior || $this->assertFalse( Memoize::isMemoized( $owner, $key ) );
        $result = Memoize::memoize( $owner, $key, $provider );

        $this->assertTrue( Memoize::isMemoized( $owner, $key ) );
        return $result;

    }

    /**
     * @param null $owner
     * @param string|null $key
     */
    protected function doUnmemoize( bool $existsPrior, $owner = null, string $key = null ): void {

        $existsPrior && $this->assertTrue( Memoize::isMemoized( $owner, $key ) );
        Memoize::unmemoize( $owner, $key );

        $this->assertFalse( Memoize::isMemoized( $owner, $key ) );

    }

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
    public function testMemoize( string $group, string $instance, string $key, State $state ): void {

        $owner = $state->owners[ $group ][ $instance ][ $key ];
        $provider = $state->providers[ $group ][ $instance ][ $key ];

        $value = $this->doMemoize( false, $owner, $key, $provider );
        $this->assertSame( $value, $provider->getValue() );

        $cached = $this->doMemoize( true, $owner, $key, $provider );
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
    public function testUnmemoizeWithKey( string $group, string $instance, string $key, State $state ): void {

        $owner = $state->owners[ $group ][ $instance ][ $key ];
        $provider = $state->providers[ $group ][ $instance ][ $key ];

        $initial = $this->doMemoize( false, $owner, $key, $provider );
        $this->doUnmemoize( true, $owner, $key );

        $reInvoked = $this->doMemoize( false, $owner, $key, $provider );
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
    public function testUnmemoizeWithoutKey( string $group, string $instance, State $state ): void {

        $owner = $state->owners[ $group ][ $instance ]['a']; // same owner for both a and b.

        $providerA = $state->providers[ $group ][ $instance ]['a'];
        $providerB = $state->providers[ $group ][ $instance ]['b'];

        $initialA = $this->doMemoize( false, $owner, 'a', $providerA );
        $initialB = $this->doMemoize( false, $owner, 'b', $providerB );

        $this->doUnmemoize( true, $owner );

        $reInvokedA = $this->doMemoize( false, $owner, 'a', $providerA );
        $reInvokedB = $this->doMemoize( false, $owner, 'b', $providerB );

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
    public function testUnmemoizeWithoutOwner( string $group, State $state ): void {

        $ownerFirst = $state->owners[ $group ]['first']['a']; // same owner for both a and b.
        $ownerSecond = $state->owners[ $group ]['second']['a']; // same owner for both a and b.

        $providerFirstA = $state->providers[ $group ]['first']['a'];
        $providerSecondA = $state->providers[ $group ]['second']['a'];

        $providerFirstB = $state->providers[ $group ]['first']['b'];
        $providerSecondB = $state->providers[ $group ]['second']['b'];

        $initialFirstA = $this->doMemoize( false, $ownerFirst, 'a', $providerFirstA );
        $initialSecondA = $this->doMemoize( false, $ownerSecond, 'a', $providerSecondA );

        $initialFirstB = $this->doMemoize( false, $ownerFirst, 'b', $providerFirstB );
        $initialSecondB = $this->doMemoize( false, $ownerSecond, 'b', $providerSecondB );

        Memoize::unmemoize();

        $reInvokedFirstA = $this->doMemoize( true, $ownerFirst, 'a', $providerFirstA );
        $reInvokedSecondA = $this->doMemoize( true, $ownerSecond, 'a', $providerSecondA );

        $reInvokedFirstB = $this->doMemoize( true, $ownerFirst, 'b', $providerFirstB );
        $reInvokedSecondB = $this->doMemoize( true, $ownerSecond, 'b', $providerSecondB );

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

    public function testUnmemoizeUnknownKey(): void {

        $this->doMemoize( false, TestClass1::class, 'a', function () {} );
        $this->doUnmemoize( false, TestClass1::class, '123' );

        $this->assertTrue( true ); // just assert no errors were thrown

    }

    public function testUnmemoizeUnknownOwner(): void {
        $this->doUnmemoize( false, TestClass1::class );
        $this->assertTrue( true ); // just assert no errors were thrown
    }

    public function testUnknownOwner(): void {
        $this->expectException( UnknownOwnerException::class );
        $this->doMemoize( false, '123', 'a', function () {} );
    }

    public function testEnableDisable(): void {

        $calls = 0;
        $provider = function () use ( &$calls ) {
            return ++$calls;
        };

        Memoize::disable();

        $this->assertEquals( 1, Memoize::memoize( $this, __METHOD__, $provider ) );
        $this->assertEquals( 2, Memoize::memoize( $this, __METHOD__, $provider ) );
        $this->assertEquals( 3, Memoize::memoize( $this, __METHOD__, $provider ) );

        $this->assertEquals( 3, $calls );
        Memoize::enable();

        $this->assertEquals( 4, Memoize::memoize( $this, __METHOD__, $provider ) );
        $this->assertEquals( 4, Memoize::memoize( $this, __METHOD__, $provider ) );

        $this->assertEquals( 4, $calls );

    }

    public function testIsMemoizedDisabled(): void {

        Memoize::memoize( __CLASS__, 'b', function (): int { return 1; } );
        Memoize::disable();

        $this->assertFalse( Memoize::isMemoized( __CLASS__, 'b' ) );

    }

    public function testIsMemoizedNoOwner(): void {
        $this->assertFalse( Memoize::isMemoized() );
    }

}
