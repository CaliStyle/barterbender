<?php


namespace Apps\Core_MobileApi\Version1_6\Api\Form\Marketplace;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\AttachmentType;
use Apps\Core_MobileApi\Api\Form\Type\CheckboxType;
use Apps\Core_MobileApi\Api\Form\Type\ChoiceType;
use Apps\Core_MobileApi\Api\Form\Type\HierarchyType;
use Apps\Core_MobileApi\Api\Form\Type\LocationType;
use Apps\Core_MobileApi\Api\Form\Type\PriceType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TextareaType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;
use Apps\Core_MobileApi\Api\Form\Validator\StringLengthValidator;
use Apps\Core_MobileApi\Api\Form\Validator\TypeValidator;
use Phpfox;


class MarketplaceForm extends GeneralForm
{

    protected $categories;
    protected $countries;
    protected $currencies;
    protected $tags;
    protected $action = "marketplace";
    protected $editing = false;

    /**
     * @param null  $options
     * @param array $data
     *
     * @return mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\ValidationErrorException
     */
    function buildForm($options = null, $data = [])
    {
        $sectionName = 'information';
        $this
            ->addSection($sectionName, 'information')
            ->addField('title', TextType::class, [
                'label'       => 'what_are_you_selling',
                'required'    => true,
                'placeholder' => 'listing_title'
            ], [new StringLengthValidator(1, 250)], $sectionName)
            ->addField('categories', HierarchyType::class, [
                'label'    => 'categories',
                'rawData'  => $this->categories,
                'multiple' => false,
                'required' => true,
            ], [new TypeValidator(TypeValidator::IS_ARRAY_NUMERIC)], $sectionName)
            ->addField('currency_id', ChoiceType::class,
                $this->getCurrencyOptions([
                    'label' => 'currency'
                ]), [new RequiredValidator()], $sectionName)
            ->addField('price', PriceType::class, [
                'label'         => 'price',
                'placeholder'   => '0.00',
                'value_default' => 0.00,
                'fieldStyle'    => ['fontWeight' => 'bold']
            ], null, $sectionName)
            ->addField('short_description', TextareaType::class, [
                'label'       => 'short_description',
                'placeholder' => 'type_something_dot'
            ], null, $sectionName)
            ->addField('text', TextareaType::class, [
                'label'       => 'description',
                'placeholder' => 'type_something_dot'
            ], null, $sectionName)
            ->addField('attachment', AttachmentType::class, [
                'label'               => 'attachment',
                'item_type'           => "marketplace",
                'item_id'             => (isset($this->data['id']) ? $this->data['id'] : null),
                'current_attachments' => $this->getAttachments()
            ], [new TypeValidator(TypeValidator::IS_ARRAY_NUMERIC)], $sectionName)
            ->addField('location', LocationType::class, [
                'label'         => 'location',
                'placeholder'   => 'enter_location',
                'use_transform' => true,
                'required'      => true
            ], [new RequiredValidator()], $sectionName);

        $sectionName = 'settings';
        $this->addSection($sectionName, 'settings');
        if ($this->setting->getUserSetting('marketplace.can_sell_items_on_marketplace')) {
            $this->addField('is_sell', CheckboxType::class, [
                'label'         => 'enable_instant_payment',
                'description'   => $this->local->translate('if_you_enable_this_option_buyers_can_make_a_payment_to_one_of_the_payment_gateways_you_have_on_file_with_us_to_manage_your_payment_gateways_go_to_system_settings_edit_account'),
                'value_default' => 0,
                'options'       => [
                    [
                        'value' => 0,
                        'label' => $this->local->translate('no')
                    ],
                    [
                        'value' => 1,
                        'label' => $this->local->translate('yes')
                    ],
                ],
            ], null, $sectionName);
            if (Phpfox::isAppActive('Core_Activity_Points') && $this->setting->getUserSetting('marketplace.point_payment_on_marketplace')) {
                $this->addField('allow_point_payment', CheckboxType::class, [
                    'label'         => 'enable_activity_point_payment',
                    'description'   => $this->local->translate('if_you_enable_this_option_buyers_can_make_a_payment_with_their_activity_points'),
                    'value_default' => 0,
                    'options'       => [
                        [
                            'value' => 0,
                            'label' => $this->local->translate('no')
                        ],
                        [
                            'value' => 1,
                            'label' => $this->local->translate('yes')
                        ],
                    ],
                ], null, $sectionName);
            }
            $this->addField('auto_sell', CheckboxType::class, [
                'label'         => 'auto_sold',
                'description'   => $this->local->translate('if_this_is_enabled_and_once_a_successful_purchase_of_this_item_is_made'),
                'value_default' => 1,
                'options'       => [
                    [
                        'value' => 0,
                        'label' => $this->local->translate('no')
                    ],
                    [
                        'value' => 1,
                        'label' => $this->local->translate('yes')
                    ],
                ],
            ], null, $sectionName);
        }
        if ($this->editing && ($this->isPost || (isset($this->data['view_id']) && ($this->data['view_id'] == 0 || $this->data['view_id'] == 2)))) {
            $this->addField('is_closed', CheckboxType::class, [
                'label'         => 'closed_item_sold',
                'description'   => $this->local->translate('enable_this_option_if_this_item_is_sold_and_this_listing_should_be_closed'),
                'value_default' => 0,
                'options'       => [
                    [
                        'value' => 0,
                        'label' => $this->local->translate('no')
                    ],
                    [
                        'value' => 1,
                        'label' => $this->local->translate('yes')
                    ],
                ],
            ], null, $sectionName);
        }
        $this
            ->addPrivacyField([
                'description' => 'control_who_can_see_this_listing'
            ], $sectionName, $this->privacy->getValue('marketplace.display_on_profile'));

        $this->addField('submit', SubmitType::class, [
            'label' => 'save'
        ]);
    }


    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * @param mixed $currencies
     */
    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getCurrencyOptions($options = [])
    {
        $currencies = $this->getCurrencies();
        if (!empty($currencies)) {
            $options['required'] = true;
            $extraOptions = [];
            foreach ($currencies as $key => $currency) {
                if ($currency['is_default']) {
                    $options['value_default'] = $key;
                }
                $extraOptions[] = [
                    'value' => $key,
                    'label' => $this->local->translate($currency['name'])
                ];
            }
            if ($extraOptions) {
                $options['options'] = $extraOptions;
            }
        }
        return $options;
    }

    public function getAttachments()
    {
        return (isset($this->data['attachments']) ? $this->data['attachments'] : null);
    }

    /**
     * @return bool
     */
    public function isEditing()
    {
        return $this->editing;
    }

    /**
     * @param bool $editing
     */
    public function setEditing($editing)
    {
        $this->editing = $editing;
    }

}