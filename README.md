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

### Per-Object Memoization
```php
class ClassUsingCaching {
    use \TonyBogdanov\Memoize\Traits\MemoizeTrait;

    public function getObjectLevelCachedThing() {
        return $this->memoize( __METHOD__, function () {
            return 'thing'; // heavy code that needs to run only once per object instance.
        } );
    }
}
```

You can also manually remove memoized values:
```php
$object->unmemoize( 'key' );
```

You can even check if a memoized value exists without retrieving it (even if it's `null`):
```php
$object->isMemoized( 'key' );
```

### Per-Class Memoization

```php
class ClassUsingCaching {
    use \TonyBogdanov\Memoize\Traits\MemoizeTrait;

    public static function getClassLevelCachedThing() {
        return static::memoizeStatic( __METHOD__, function () {
            return 'thing'; // heavy code that needs to run only once per class.
        } );
    }
}
```

You can also manually remove memoized values:
```php
StaticClass::unmemoizeStatic( 'key' );
```

You can even check if a memoized value exists without retrieving it (even if it's `null`):
```php
StaticClass::isMemoizedStatic( 'key' );
```

### Foreign Objects

As of `2.3` you can access and manage the memoized values of foreign objects / classes as well.
```php
// per-object
$this->memoizeForeign( $object, 'key', 'value' );
$this->unmemoizeForeign( $object, 'key' );
$this->isMemoizedForeign( $object, 'key' );

// per-class
StaticClass::memoizeStaticForeign( AnotherStaticClass::class, 'key', 'value' );
StaticClass::unmemoizeStaticForeign( AnotherStaticClass::class, 'key' );
StaticClass::isMemoizedStaticForeign( AnotherStaticClass::class, 'key' );
```

### Toggle Memoization

You can toggle memoization globally, which can be useful for testing:
```php
Memoize::enable();
Memoize::disable();
```
