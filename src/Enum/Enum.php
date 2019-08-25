<?php

namespace App\Enum;

use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use ReflectionClass;
use ReflectionException;

abstract class Enum
{
	/**
	 * Remembers the enum objects per enum type. This allows for identical checks to be valid.
	 *
	 * @var Enum[][]
	 */
	private static $enum_instances = [];

	/**
	 * Remembers the constants per enum type. Retrieving the constants is done by reflection, this could be considered expensive for the resources.
	 *
	 * @var string[][]
	 */
	private static $enum_constants = [];

	/**
	 * @var string
	 */
	private $constant;

	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @param string $constant
	 * @param mixed $value
	 */
	final private function __construct($constant, $value)
	{
		$this->constant = $constant;
		$this->value = $value;
	}

	/**
	 * @param $value
	 *
	 * @return $this
	 *
	 * @throws EnumNotDefinedException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 */
	public static function createEnum($value)
	{
		$constant_values = array_flip(self::getConstants());

		if (!isset($constant_values[$value]))
		{
			throw new EnumNotDefinedException(sprintf('Constant value %s is not defined, defined constant values: %s', $value, implode(',', array_keys($constant_values))));
		}

		$constant = $constant_values[$value];

		return self::$constant();
	}

	/**
	 * @param string $name
	 * @param mixed $arguments
	 *
	 * @return $this
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 */
	public static function __callStatic($name, $arguments)
	{
		$constants = self::getConstants();

		if (!isset($constants[$name]))
		{
			throw new EnumNotDefinedException(sprintf('Constant %s is not defined, defined constants: %s', $name, implode(',', array_keys($constants))));
		}

		return self::getEnumInstance($name);
	}

	/**
	 * @return string[]
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 */
	public static function getConstants()
	{
		$class = get_called_class();

		if (!isset(self::$enum_constants[$class]))
		{
			try
			{
				$reflection = new ReflectionClass($class);
				self::$enum_constants[$class] = $reflection->getConstants();
			}
			catch (ReflectionException $e)
			{
				throw new EnumConstantsCouldNotBeResolvedException(sprintf('Constants could not be resolved for class %s', $class), 0, $e);
			}
		}

		return self::$enum_constants[$class];
	}

	/**
	 * @return $this[]
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 */
	public static function getValues()
	{
		return array_map(function($name) {
			return self::$name();
		}, array_flip(self::getConstants()));
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 */
	private static function getEnumInstance($name)
	{
		$class = get_called_class();

		if (!isset(self::$enum_instances[$class][$name]))
		{
			$constants = self::getConstants();
			self::$enum_instances[$class][$name] = new $class($name, $constants[$name]);
		}

		return self::$enum_instances[$class][$name];
	}

	/**
	 * @return string
	 */
	public function getConstant()
	{
		return $this->constant;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		return $this->value;
	}

	/**
	 * @param Enum|null $enum
	 *
	 * @return bool
	 */
	public function equals(Enum $enum = null)
	{
		return $enum !== null && get_class($enum) === get_class($this)
			&& $enum->getValue() === $this->getValue()
			&& $enum->getConstant() === $this->getConstant();
	}
}