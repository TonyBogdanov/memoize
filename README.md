# Object & class-level in-memory caching

[![Latest Stable Version](https://poser.pugx.org/tonybogdanov/memoize/v/stable)](https://packagist.org/packages/tonybogdanov/memoize)
[![License](https://poser.pugx.org/tonybogdanov/memoize/license)](https://packagist.org/packages/tonybogdanov/memoize)
![Build](https://github.com/tonybogdanov/memoize/workflows/build/badge.svg)
[![Coverage](http://tonybogdanov.github.io/memoize/coverage.svg)](http://tonybogdanov.github.io/memoize/index.html)

## Installation

```bash
composer require tonybogdanov/memoize:^2.0
```

## Usage

```php
class ClassUsingCaching {

    use \TonyBogdanov\Memoize\Traits\MemoizeTrait;

    public static function getClassLevelCachedThing() {

        return static::memoizeStatic( __METHOD__, function () {

            // heavy code that needs to run only once per class.
            return 'thing';

        } );
    
    }

    public function getObjectLevelCachedThing() {

        return $this->memoize( __METHOD__, function () {

            // heavy code that needs to run only once per object instance.
            return 'thing';

        } );
    
    }

}
```

You can also manually removed memoized values:

```php
StaticClass::unmemoizeStatic( 'key' );
$object->unmemoize( 'key' );
```

You can also toggle memoization globally, which can be useful for testing:

```php
Memoize::enable();
Memoize::disable();
```
