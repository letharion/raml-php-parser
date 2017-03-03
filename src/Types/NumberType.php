<?php

namespace Raml\Types;

use Raml\Exception\TypeValidationException;
use Raml\Type;

/**
 * NumberType class
 *
 * @author Melvin Loos <m.loos@infopact.nl>
 */
class NumberType extends Type
{
    /**
     * The minimum value of the parameter. Applicable only to parameters of type number or integer.
     *
     * @var int
     **/
    private $minimum = null;

    /**
     * The maximum value of the parameter. Applicable only to parameters of type number or integer.
     *
     * @var int
     **/
    private $maximum = null;

    /**
     * The format of the value. The value MUST be one of the following: int32, int64, int, long, float, double, int16, int8
     *
     * @var string
     **/
    private $format = null;

    /**
     * A numeric instance is valid against "multipleOf" if the result of dividing the instance by this keyword's value is an integer.
     *
     * @var int
     **/
    private $multipleOf = null;

    /**
    * Create a new NumberType from an array of data
    *
    * @param string    $name
    * @param array     $data
    *
    * @return NumberType
    */
    public static function createFromArray($name, array $data = [])
    {
        $type = parent::createFromArray($name, $data);

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'minimum':
                    $type->setMinimum($value);
                    break;
                case 'maximum':
                    $type->setMaximum($value);
                    break;
                case 'format':
                    $type->setFormat($value);
                    break;
                case 'multipleOf':
                    $type->setMultipleOf($value);
                    break;
            }
        }

        return $type;
    }

    /**
     * Get the value of Minimum
     *
     * @return int
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * Set the value of Minimum
     *
     * @param int $minimum
     *
     * @return self
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;

        return $this;
    }

    /**
     * Get the value of Maximum
     *
     * @return int
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * Set the value of Maximum
     *
     * @param int $maximum
     *
     * @return self
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;

        return $this;
    }

    /**
     * Get the value of Format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the value of Format
     *
     * @param string $format
     *
     * @return self
     * @throws Exception Thrown when given format is not any of allowed types.
     */
    public function setFormat($format)
    {
        if (!in_array($format, ['int32', 'int64', 'int', 'long', 'float', 'double', 'int16', 'int8'])) {
            throw new \Exception(sprinf('Incorrect format given: "%s"', $format));
        }
        $this->format = $format;

        return $this;
    }

    /**
     * Get the value of Multiple Of
     *
     * @return int
     */
    public function getMultipleOf()
    {
        return $this->multipleOf;
    }

    /**
     * Set the value of Multiple Of
     *
     * @param int $multipleOf
     *
     * @return self
     */
    public function setMultipleOf($multipleOf)
    {
        $this->multipleOf = $multipleOf;

        return $this;
    }

    public function validate($value)
    {
        if (!is_null($this->maximum)) {
            if ($value > $this->maximum) {
                return false;
            }
        }
        if (!is_null($this->minimum)) {
            if ($value < $this->minimum) {
                return false;
            }
        }
        switch ($this->format) {
            case 'int8':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => -128, 'max_range' => 127]]) === false) {
                    throw TypeValidationException::unexpectedValueType($this->format, $value);
                }
                break;
            case 'int16':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => -32768, 'max_range' => 32767]]) === false) {
                    throw TypeValidationException::unexpectedValueType($this->format, $value);
                }
                break;
            case 'int32':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => -2147483648, 'max_range' => 2147483647]]) === false) {
                    throw TypeValidationException::unexpectedValueType($this->format, $value);
                }
                break;
            case 'int64':
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => -9223372036854775808, 'max_range' => 9223372036854775807]]) === false) {
                    throw TypeValidationException::unexpectedValueType($this->format, $value);
                }
                break;
            case 'int':
                // int === long
            case 'long':
                if (!is_int($value)) {
                    throw TypeValidationException::unexpectedValueType($this->format, $value);
                }
                break;
            case 'float':
                // float === double
            case 'double':
                if (!is_float($value)) {
                    throw TypeValidationException::unexpectedValueType($this->format, $value);
                }
                break;
            // if no format is given we check only if it is a number
            null:
            default:
                if (!is_float($value) && !is_int($value)) {
                    throw TypeValidationException::unexpectedValueType('number', $value);
                }
                break;
        }
        if (!is_null($this->multipleOf)) {
            if ($value %$this->multipleOf != 0) {
                return false;
            }
        }
        return true;
    }
}
