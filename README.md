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

$class = new ClassInspector(new TestClass(), ClassInspector::MODE_ALL);

// Now you can access private and protected parts of the oryginal class as if they were public
var_dump($class->privateProperty);
$class->privateProperty = null;
unset($class->privateProperty);
$class->privateMethod();
```

## Modes
`ClassInspector::MODE_READ` - allows reading and isset() for properties (this is the default mode)  
`ClassInspector::MODE_WRITE` - allows writing and unset() for properties  
`ClassInspector::MODE_CALL` - allows calls to methods  
`ClassInspector::MODE_ALL` - shortcut for `ClassInspector::MODE_READ | ClassInspector::MODE_WRITE | ClassInspector::MODE_CALL`

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

$class = new ClassInspector(new ChildClass(), ClassInspector::MODE_ALL, ParentClass::class);
var_dump($class->parentPrivateProperty); // will work
var_dump($class->childPrivateProperty); // won't work
```

The same applies to methods

## License
ClassInspector is published under the [MIT](LICENSE.txt) license
