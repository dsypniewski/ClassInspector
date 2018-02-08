# ClassInspector
ClassInspector is a simple utility class that allows access to private members of a class instance.
Can be useful when you don't want to use reflections but still need to somehow access that sweat private/protected data or for testing purposes, whatever floats your boat.

## Usage
```php
<?php

class TestClass
{
	private $privateProperty = 1;
	private function privateMethod() { return 2; }
}

$i = new ClassInspector(new TestClass());

// Now you can access private and protected parts of the original class as if they were public
$i->privateProperty;
$i->privateProperty = null;
isset($i->privateProperty);
unset($i->privateProperty);
$i->privateMethod();
```

## Scope
If you want to access a private properties of one of the parent classes you need to specify the third parameter with the class name.
```php
<?php

class ParentClass
{
    private $parentPrivateProperty = 1;
}

class ChildClass extends ParentClass
{
    private $childPrivateProperty = 2;
}

$i = new ClassInspector(new ChildClass(), ParentClass::class);
var_dump($i->parentPrivateProperty);
$i(ChildClass::class); // Need to change scope for the next line to work
var_dump($i->childPrivateProperty);
```

## Static inspector
Example of inspecting static properties/methods
```php
<?php

class TestClass
{
	private static $privateProperty = 1;
	private static function privateMethod() { return 2; }
}

$i = ClassInspector::staticInspector(TestClass::class);

// Now you can access private and protected parts of the original class as if they were public
$i->privateProperty;
$i->privateProperty = null;
isset($i->privateProperty);
// Unset for static properties is not supported
$i->privateMethod();
```

## License
ClassInspector is published under the [MIT](LICENSE.txt) license
