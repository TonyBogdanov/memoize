<?php

namespace TonyBogdanov\Memoize\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Prophecy\Call\Call;
use stdClass;

/**
 * Class State
 *
 * @package TonyBogdanov\Memoize\Tests\Helper
 */
class State {

    /**
     * @var array
     */
    public array $providers;

    /**
     * @var array
     */
    public array $owners;

    /**
     * @var array
     */
    public array $invoked;

    /**
     * @var array
     */
    public array $reInvoked;

    /**
     * State constructor.
     */
    public function __construct() {

        $this->providers = [

            'class' => [

                'first' => [ 'a' => new Callback( new stdClass() ), 'b' => new Callback( new stdClass() ) ],
                'second' => [ 'a' => new Callback( new stdClass() ), 'b' => new Callback( new stdClass() ) ],

            ],

            'object' => [

                'first' => [ 'a' => new Callback( new stdClass() ), 'b' => new Callback( new stdClass() ) ],
                'second' => [ 'a' => new Callback( new stdClass() ), 'b' => new Callback( new stdClass() ) ],

            ],

        ];

        $firstObject = new TestClass1();
        $secondObject = new TestClass2();

        $this->owners = [

            'class' => [

                'first' => [ 'a' => TestClass1::class, 'b' => TestClass1::class ],
                'second' => [ 'a' => TestClass2::class, 'b' => TestClass2::class ],

            ],

            'object' => [

                'first' => [ 'a' => $firstObject, 'b' => $firstObject ],
                'second' => [ 'a' => $secondObject, 'b' => $secondObject ],

            ],

        ];

        $this->invoked = [

            'class' => [

                'first' => [ 'a' => false, 'b' => false ],
                'second' => [ 'a' => false, 'b' => false ],

            ],

            'object' => [

                'first' => [ 'a' => false, 'b' => false ],
                'second' => [ 'a' => false, 'b' => false ],

            ],

        ];

        $this->reInvoked = [

            'class' => [

                'first' => [ 'a' => false, 'b' => false ],
                'second' => [ 'a' => false, 'b' => false ],

            ],

            'object' => [

                'first' => [ 'a' => false, 'b' => false ],
                'second' => [ 'a' => false, 'b' => false ],

            ],

        ];

    }

    /**
     * @param TestCase $testCase
     */
    public function assert( TestCase $testCase ) {

        foreach ( [ 'class', 'object' ] as $owner ) {

            foreach ( [ 'first', 'second' ] as $instance ) {

                foreach ( [ 'a', 'b' ] as $key ) {

                    $testCase->assertSame(

                        ( $this->invoked[ $owner ][ $instance ][ $key ] ? 1 : 0 ) +
                        ( $this->reInvoked[ $owner ][ $instance ][ $key ] ? 1 : 0 ),

                        $this->providers[ $owner ][ $instance ][ $key ]->getInvocations()

                    );

                }

            }

        }

    }

}
