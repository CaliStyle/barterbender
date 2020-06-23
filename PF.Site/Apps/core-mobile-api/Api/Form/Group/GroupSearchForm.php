<?php


namespace Apps\Core_MobileApi\Api\Form\Group;

use Apps\Core_MobileApi\Api\Form\SearchForm;


class GroupSearchForm extends SearchForm
{
    public function getSortOptions()
    {
        return [
            [
                'value' => 'latest',
                'label' => $this->local->translate('latest')
            ],
            [
                'value' => 'most_liked',
                'label' => $this->local->translate('most_liked')
            ],
        ];
    }
}