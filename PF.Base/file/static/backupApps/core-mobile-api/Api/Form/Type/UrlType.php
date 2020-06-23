<?php

namespace Apps\Core_MobileApi\Api\Form\Type;


class UrlType extends GeneralType
{
    protected $componentName = "Url";

    protected $attrs = [
        'returnKeyType' => 'next'
    ];

    public function getAvailableAttributes()
    {
        return [
            'label',
            'value',
            'returnKeyType',
            'preview_endpoint'
        ];
    }
}