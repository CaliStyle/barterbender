<?php
namespace Apps\P_AdvMarketplaceAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\CheckboxType;
use Apps\Core_MobileApi\Api\Form\Type\DateType;
use Apps\Core_MobileApi\Api\Form\Type\MultiChoiceType;
use Apps\Core_MobileApi\Api\Form\Type\TextareaType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Validator\StringLengthValidator;
use Apps\Core_MobileApi\Api\Form\Validator\TypeValidator;
use Apps\Core_MobileApi\Api\Form\Type\HierarchyType;
use Apps\Core_MobileApi\Api\Form\Type\TagsType;
use Apps\Core_MobileApi\Api\Form\Type\PriceType;
use Apps\Core_MobileApi\Api\Form\Type\ChoiceType;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;
use Apps\Core_MobileApi\Api\Form\Type\FileType;

class MarketplaceForm extends GeneralForm
{
    protected $categories;
    protected $currencies;
    protected $isEdit;
    protected $paymentMethods;
    protected $isDraft;
    protected $isPaymentMethodRequired = true;

    /**
     * @return mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\ValidationErrorException
     */
    public function buildForm()
    {
        $sectionName = 'information';
        $this->addSection($sectionName, 'information');

        $this->addField('title', TextType::class, [
            'label' => 'advancedmarketplace.what_are_you_selling',
            'placeholder' => 'listing_title',
            'required' => true
        ], null, $sectionName)
        ->addField('categories', HierarchyType::class, [
        'rawData' => $this->getCategories(),
        'label' => 'categories',
        'value_type' => 'array',
        'required' => true
        ], [new TypeValidator(TypeValidator::IS_ARRAY_NUMERIC)], $sectionName)
        ->addField('tags', TagsType::class, [
            'label' => 'tags',
            'description' => 'insert_product_brand_model_etc'
        ], [new TypeValidator(TypeValidator::IS_STRING)], $sectionName)
        ->addField('currency_id', ChoiceType::class,
            $this->getCurrencyOptions([
                'label' => 'currency'
            ]), [new RequiredValidator()], $sectionName)
        ->addField('price', PriceType::class, [
            'label' => 'price',
            'placeholder' => '0.00',
            'value_default' => 0.00,
            'fieldStyle' => ['fontWeight' => 'bold']
        ], null, $sectionName)
        ->addField('short_description',TextareaType::class, [
            'label' => 'short_description',
            'placeholder' => 'maximum_200_characters'
        ], null, $sectionName)
        ->addField('text', TextareaType::class,[
            'label' => 'description',
            'placeholder' => 'type_something_dot'
        ], null, $sectionName)
        ->addCountryField(true, 'location', $sectionName)
        ->addField('location', TextType::class, [
            'label' => 'location_venue',
            'inline' => true,
            'placeholder' => '',
        ], null, $sectionName)
        ->addField('address', TextType::class, [
            'label' => 'address',
            'inline' => true,
            'placeholder' => '',
        ], null, $sectionName)
        ->addField('city', TextType::class, [
            'label' => 'city',
            'inline' => true,
            'placeholder' => 'city_name',
        ], null, $sectionName)
        ->addField('postal_code', TextType::class, [
            'label' => 'postal_code',
            'inline' => true,
            'placeholder' => '- - - - - -'
        ], null, $sectionName);

        if(!$this->isEdit) {
            $this->addField('image', FileType::class,[
                'label' => 'featured_photo',
                'item_type' => 'advancedmarketplace',
                'file_type' => 'photo',
                'max_upload_filesize' => $this->getSizeLimit($this->setting->getUserSetting('advancedmarketplace.max_upload_size_listings')),
                'required' => true
            ], null, $sectionName);
        }

        $sectionName = 'settings';
        $this->addSection($sectionName, 'settings');
        if($this->setting->getUserSetting('advancedmarketplace.can_sell_items_on_advancedmarketplace')) {
            $this->addField('is_sell', CheckboxType::class,[
                'label' => 'advancedmarketplace.instant_payment',
                'description' => 'instant_payment_description'
            ], null, $sectionName);
            $this->addField('payment_methods', MultiChoiceType::class,[
                'label' => 'payment_methods',
                'options' => $this->getPaymentMethods(),
                'required' => $this->isPaymentMethodRequired,
                'description' => 'payment_method_is_required'
            ], [new TypeValidator(TypeValidator::IS_ARRAY_STRING)], $sectionName);
        }

        $this->addField('expired_date', DateType::class, [
            'label' => 'advancedmarketplace.set_expiry_date',
            'description' => 'advancedmarketplace_set_expiry_date_description',
        ]);

        if($this->setting->getUserSetting('advancedmarketplace.can_sell_items_on_advancedmarketplace')) {
            $this->addField('auto_sell', CheckboxType::class, [
                'label' => 'advancedmarketplace.auto_sold',
                'description' => 'if_this_is_enabled_and_once_a_successful_purchase_of_this_item_is_made'
            ]);
        }

        if($this->isEdit) {
            $this->addField('mark_sold', CheckboxType::class, [
               'label' => 'closed_item_sold',
                'description' => 'enable_this_option_if_this_item_is_sold_and_this_listing_should_be_closed'
            ]);
        }

        if (empty($this->data['item_id'])) {
            $this->addPrivacyField([
                'description' => 'control_who_can_see_this_listing',
                'required' => true
            ], null, $this->privacy->getValue('advancedmarketplace.display_on_profile'));
        }

        $this
            ->addModuleFields([
                'module_value' => 'advancedmarketplace',
                'item_value' => 0
            ]);

        if(!$this->isEdit || $this->isDraft)
        $this->addField('is_draft', CheckboxType::class, [
             'label' => 'save_as_draft',
             'description' => 'advancedmarketplace_save_as_draft_tips'
        ]);


    }

    /**
     * @return mixed
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    public function setCurrencies($currencies)
    {
        $this->currencies = $currencies;
        return $this;
    }

    public function setIsEdit($isEdit)
    {
        $this->isEdit = isset($isEdit) ? $isEdit : false;
        return $this;
    }

    public function getIsEdit()
    {
        return $this->isEdit;
    }

    public function setPaymentMethods($paymentMethods)
    {
        $this->paymentMethods = !empty($paymentMethods) ? $paymentMethods : [];
        return $this;
    }

    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    public function setIsDraft($isDraft)
    {
        $this->isDraft = isset($isDraft) ? $isDraft : false;
        return $this;
    }

    public function getIsDraft()
    {
        return $this->isDraft;
    }

    public function setIsPaymentMethodRequired($isRequired)
    {
        $this->isPaymentMethodRequired = (bool)$isRequired;
    }

    /**
     * @param array $options
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


    /**
     * @param $categories
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }
}