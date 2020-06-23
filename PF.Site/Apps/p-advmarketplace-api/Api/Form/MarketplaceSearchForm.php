<?php


namespace Apps\P_AdvMarketplaceAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\SearchForm;


class MarketplaceSearchForm extends SearchForm
{
    public function getSortOptions()
    {
        $sortOptions = parent::getSortOptions();
        $sortOptions[] = [
            'value' => 'low_high_price',
            'label' => $this->local->translate('low_high_price')
        ];
        $sortOptions[] = [
            'value' => 'high_low_price',
            'label' => $this->local->translate('high_low_price')
        ];
        $sortOptions[] = [
            'value' => 'featured',
            'label' => $this->local->translate('featured')
        ];
        $sortOptions[] = [
            'value' => 'sponsored',
            'label' => $this->local->translate('sponsored')
        ];


        return $sortOptions;
    }
}