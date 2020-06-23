<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 28/5/18
 * Time: 10:04 AM
 */

namespace Apps\Core_MobileApi\Api\Form\Type;


use Apps\Core_MobileApi\Api\Form\Validator\DateTimeFormatValidator;

class DateType extends GeneralType
{
    const FORMAT = "Y-m-d";

    protected $componentName = 'Date';

    protected $attrs = [
        'returnKeyType' => 'next'
    ];

    public function setValidators($validators)
    {
        $validators[] = new DateTimeFormatValidator(self::FORMAT);
        return parent::setValidators($validators);
    }
}