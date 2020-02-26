# Object & class-level in-memory caching

[![Latest Stable Version](https://poser.pugx.org/tonybogdanov/memoize/v/stable)](https://packagist.org/packages/tonybogdanov/memoize)
[![License](https://poser.pugx.org/tonybogdanov/memoize/license)](https://packagist.org/packages/tonybogdanov/memoize)

## Installation

```bash
composer require tonybogdanov/memoize
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
StaticClass::dememoizeStatic( 'key' );
$object->dememoize( 'key' );
```
