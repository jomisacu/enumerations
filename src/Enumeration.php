<?php
/**
 * @author Jorge Miguel Sanchez Cuello <jomisacu.software@gmail.com>
 *
 * Date: 2021-11-26 11:55
 */

declare(strict_types=1);

namespace Jomisacu\Enumerations;

use Exception;
use ReflectionClass;

abstract class Enumeration
{
    /**
     * @var array<string,array<Enumeration>>
     */
    private static $instances = [];
    private $name;
    private $value;

    /**
     * @throws WrongEnumerationValueException
     * @throws WrongEnumerationValueTypeException
     */
    final private function __construct($name, $value)
    {
        self::throwIfWrongValue($value);

        $this->value = $value;
        $this->name = $name;

        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = [];
        }

        self::$instances[static::class][] = $this;
    }

    /**
     * @throws WrongEnumerationValueTypeException
     * @throws WrongEnumerationValueException
     */
    private static function throwIfWrongValue($value)
    {
        if (!is_string($value) && !is_numeric($value)) {
            throw new WrongEnumerationValueTypeException();
        }

        if (false == in_array($value, self::getConstants())) {
            throw new WrongEnumerationValueException();
        }
    }

    private static function getConstants(): array
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getConstants();
    }

    /**
     * @param $name
     *
     * @return string|int
     *
     * @throws WrongEnumerationNameException
     */
    public static function getValueFromName($name)
    {
        $name = strtolower($name);
        foreach (self::getAll() as $enumeration) {
            if ($enumeration->getName(true) == $name) {
                return $enumeration->getValue();
            }
        }

        throw new WrongEnumerationNameException();
    }

    /**
     * @return array<static>
     */
    public static function getAll(): array
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            foreach (self::getConstants() as $name => $value) {
                new static($name, $value);
            }
        }

        return self::$instances[$class] ?? [];
    }

    public static function getAllValues(): array
    {
        return array_values(self::getConstants());
    }

    public function getName($lowercase = false): string
    {
        return $lowercase ? strtolower($this->name) : $this->name;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    public static function has(Enumeration $enumeration): bool
    {
        return in_array($enumeration, self::getAll());
    }

    public static function hasValue($value): bool
    {
        try {
            self::throwIfWrongValue($value);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @throws WrongEnumerationValueException
     * @throws WrongEnumerationValueTypeException
     *
     * @return static
     */
    public static function fromValue($value)
    {
        self::throwIfWrongValue($value);

        foreach (self::getAll() as $item) {
            if ($item->getValue() == $value) {
                return $item;
            }
        }

        throw new WrongEnumerationValueException();
    }

    /**
     * @throws WrongEnumerationNameException
     * @throws WrongEnumerationValueException
     * @throws WrongEnumerationValueTypeException
     */
    public static function __callStatic($name, $arguments)
    {
        return self::fromName($name);
    }

    /**
     * @return Enumeration
     *
     * @throws WrongEnumerationNameException
     * @throws WrongEnumerationValueException
     * @throws WrongEnumerationValueTypeException
     */
    public static function fromName(string $name): self
    {
        $name = strtolower($name);
        foreach (self::getAll() as $enumeration) {
            if ($enumeration->getName(true) == $name) {
                return $enumeration;
            }
        }

        throw new WrongEnumerationNameException();
    }

    public function is($value): bool
    {
        return $this->equalsToValue($value);
    }

    public function equalsToValue($value): bool
    {
        return $this->getValue() == $value;
    }

    public function equals(Enumeration $enumeration): bool
    {
        return $this->getValue() == $enumeration->getValue();
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }
}
