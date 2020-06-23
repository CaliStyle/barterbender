<?php


namespace Apps\Core_MobileApi\Api\Form\Event;

use Apps\Core_MobileApi\Api\Form\SearchForm;

class EventSearchForm extends SearchForm
{

    public function addExtraField()
    {
//        $this->addCountryField(false, 'country');
    }

    public function getWhenOptions()
    {
        return [
            [
                'value' => 'all-time',
                'label' => $this->local->translate('all_time')
            ],
            [
                'value' => 'today',
                'label' => $this->local->translate('today')
            ],
            [
                'value' => 'this-week',
                'label' => $this->local->translate('this_week')
            ],
            [
                'value' => 'this-month',
                'label' => $this->local->translate('this_month')
            ],
            [
                'value' => 'upcoming',
                'label' => $this->local->translate('upcoming')
            ],
            [
                'value' => 'ongoing',
                'label' => $this->local->translate('ongoing')
            ],
        ];
    }
}