<?php

namespace Apps\Core_MobileApi\Api\Form\Type;


use Apps\Core_MobileApi\Api\Form\Validator\PatternValidator;

class PhoneNumberType extends GeneralType implements FormTypeInterface
{
    protected $componentName = 'PhoneNumber';

    protected $attrs = [
        'returnKeyType' => 'next'
    ];

    public function setAttrs($attrs)
    {
        if (empty($attrs['keyboard_type'])) {
            $attrs['keyboard_type'] = 'numeric';
        }
        return parent::setAttrs($attrs);
    }

    public function setValidators($validators)
    {
        $validators[] = new PatternValidator(PatternValidator::PHONE_NUMBER);
        return parent::setValidators($validators);
    }
}