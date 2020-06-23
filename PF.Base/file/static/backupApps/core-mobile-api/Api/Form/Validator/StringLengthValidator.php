<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 25/5/18
 * Time: 6:14 PM
 */

namespace Apps\Core_MobileApi\Api\Form\Validator;


class StringLengthValidator implements ValidateInterface
{
    const UNLIMITED = -1;

    protected $message;

    protected $min;
    protected $max;

    public function __construct($min, $max = self::UNLIMITED, $errorMessage = null)
    {
        $this->message = $errorMessage;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @inheritdoc
     */
    function validate($value)
    {
        if (mb_strlen($value) < $this->min) {
            return false;
        }
        if ($this->max != self::UNLIMITED && mb_strlen($value) > $this->max) {
            return false;
        }
        return true;
    }

    /**
     * Get error message
     * @return string
     */
    function getError()
    {
        return $this->message;
    }
}