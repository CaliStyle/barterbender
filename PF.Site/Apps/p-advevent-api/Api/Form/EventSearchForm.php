<?php


namespace Apps\P_AdvEventAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\SearchForm;


class EventSearchForm extends SearchForm
{
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
                'value' => 'tomorrow',
                'label' => $this->local->translate('tomorrow')
            ],
            [
                'value' => 'this-week',
                'label' => $this->local->translate('this_week')
            ],
            [
                'value' => 'this-weekend',
                'label' => $this->local->translate('this_weekend')
            ],
            [
                'value' => 'next-week',
                'label' => $this->local->translate('next_week')
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