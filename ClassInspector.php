<?php

/**
 * Class ClassInspector
 */
class ClassInspector
{

	private $get;
	private $set;
	private $call;
	private $isset;
	private $unset;

	const MODE_CALL = 0b001;
	const MODE_WRITE = 0b010;
	const MODE_READ = 0b100;
	const MODE_ALL = 0b111;
	const ERROR_MSG = 'Current mode prohibits this operation type';

	/**
	 * ClassInspector constructor.
	 * @param object $object
	 * @param int $mode
	 * @param null|string $scope
	 * @throws \InvalidArgumentException
	 */
	public function __construct($object, $mode = self::MODE_READ, $scope = null)
	{
		if (!is_object($object)) {
			throw new \InvalidArgumentException('First argument must be an object');
		}

		if ($scope === null) {
			$scope = get_class($object);
		}

		if ($mode & self::MODE_READ !== 0) {
			$this->get = (function ($name) {
				return $this->{$name};
			})->bindTo($object, $scope);

			$this->isset = (function ($name) {
				return isset($this->{$name});
			})->bindTo($object, $scope);
		}

		if ($mode & self::MODE_WRITE !== 0) {
			$this->set = (function ($name, $value) {
				$this->{$name} = $value;
			})->bindTo($object, $scope);

			$this->unset = (function ($name) {
				unset($this->{$name});
			})->bindTo($object, $scope);
		}

		if ($mode & self::MODE_CALL !== 0) {
			$this->call = (function ($name, $args) {
				return $this->{$name}(...$args);
			})->bindTo($object, $scope);
		}
	}

	/**
	 * @param string $name
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get($name)
	{
		if (!isset($this->get)) {
			throw new \Exception(self::ERROR_MSG);
		}

		return ($this->get)($name);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 * @throws \Exception
	 */
	public function __set($name, $value)
	{
		if (!isset($this->set)) {
			throw new \Exception(self::ERROR_MSG);
		}

		($this->set)($name, $value);
	}

	/**
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 * @throws \Exception
	 */
	public function __call($name, $args)
	{
		if (!isset($this->call)) {
			throw new \Exception(self::ERROR_MSG);
		}

		return ($this->call)($name, $args);
	}

	/**
	 * @param string $name
	 * @return mixed
	 * @throws \Exception
	 */
	public function __isset($name)
	{
		if (!isset($this->isset)) {
			throw new \Exception(self::ERROR_MSG);
		}

		return ($this->isset)($name);
	}

	/**
	 * @param string $name
	 * @return void
	 * @throws \Exception
	 */
	public function __unset($name)
	{
		if (!isset($this->unset)) {
			throw new \Exception(self::ERROR_MSG);
		}

		($this->unset)($name);
	}

}
