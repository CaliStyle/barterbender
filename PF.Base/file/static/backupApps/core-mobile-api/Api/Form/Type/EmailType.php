<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 28/5/18
 * Time: 10:07 AM
 */

namespace Apps\Core_MobileApi\Api\Form\Type;


class EmailType extends TextType
{
    protected $componentName = 'Email';

    protected $attrs = [
        'returnKeyType' => 'next'
    ];

    public function setAttrs($attrs)
    {
        $attrs['keyboard_type'] = 'email-address';
        return parent::setAttrs($attrs);
    }
}