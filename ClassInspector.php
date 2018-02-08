<?php

/**
 * Class ClassInspector
 */
class ClassInspector
{

	protected $get;
	protected $set;
	protected $call;
	protected $isset;
	protected $unset;
	protected $object;

	/**
	 * ClassInspector constructor.
	 * @param object $object
	 * @param string|object $scope
	 * @throws \InvalidArgumentException
	 */
	public function __construct($object, $scope = null)
	{
		if (!is_object($object)) {
			throw new \InvalidArgumentException('First argument must be an object');
		}

		$this->object = $object;
		$this->bind($scope === null ? get_class($object) : $scope);
	}

	/**
	 * @param string|object $scope
	 * @return void
	 */
	public function __invoke($scope)
	{
		$this->bind($scope);
	}

	/**
	 * @param string|object $scope
	 * @return void
	 */
	protected function bind($scope)
	{
		$this->get = (function ($name) { return $this->{$name}; })->bindTo($this->object, $scope);
		$this->set = (function ($name, $value) { $this->{$name} = $value; })->bindTo($this->object, $scope);
		$this->isset = (function ($name) { return isset($this->{$name}); })->bindTo($this->object, $scope);
		$this->unset = (function ($name) { unset($this->{$name}); })->bindTo($this->object, $scope);
		$this->call = (function ($name, $args) { return $this->{$name}(...$args); })->bindTo($this->object, $scope);
	}

	/**
	 * @param string|object $class
	 * @return object
	 */
	public static function staticInspector($class)
	{
		return new class(new stdClass, $class) extends ClassInspector
		{
		
			protected static $callStatic;

			/**
			 * @param object|string $scope
			 */
			protected function bind($scope)
			{
				$this->get = (static function ($name) { return self::${$name}; })->bindTo(null, $scope);
				$this->set = (static function ($name, $value) { self::${$name} = $value; })->bindTo(null, $scope);
				$this->isset = (static function ($name) { return isset(self::${$name}); })->bindTo(null, $scope);
				self::$callStatic = (static function ($name, $args) { return self::{$name}(...$args); })->bindTo(null, $scope);
			}

			/**
			 * @param string $name
			 * @param array $args
			 * @return mixed
			 */
			public static function __callStatic($name, $args)
			{
				return (self::$callStatic)($name, $args);
			}

			/**
			 * @param object|string $scope
			 * @return void
			 * @throws \Exception
			 */
			public function __invoke($scope)
			{
				throw new \Exception('Unable to change scope for static ClassInspector');
			}

			/**
			 * @param string $name
			 * @param array $args
			 * @return void
			 * @throws \Exception
			 */
			public function __call($name, $args)
			{
				throw new \Exception('Invalid call method for static ClassInspector, please use :: operator');
			}

			/**
			 * @param string $name
			 * @return void
			 * @throws \Exception
			 */
			public function __unset($name)
			{
				throw new \Exception('Unset operation is not supported for static properties');
			}
		
		};
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return ($this->get)($name);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		($this->set)($name, $value);
	}

	/**
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		return ($this->call)($name, $args);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __isset($name)
	{
		return ($this->isset)($name);
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function __unset($name)
	{
		($this->unset)($name);
	}

}
