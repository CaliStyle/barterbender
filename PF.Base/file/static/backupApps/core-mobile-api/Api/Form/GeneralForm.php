<?php

namespace Apps\Core_MobileApi\Api\Form;

use Apps\Core_MobileApi\Adapter\Localization\LocalizationInterface;
use Apps\Core_MobileApi\Adapter\Parse\ParseInterface;
use Apps\Core_MobileApi\Adapter\Privacy\UserPrivacyInterface;
use Apps\Core_MobileApi\Adapter\Setting\SettingInterface;
use Apps\Core_MobileApi\Adapter\Utility\ArrayUtility;
use Apps\Core_MobileApi\Api\ApiRequestInterface;
use Apps\Core_MobileApi\Api\Exception\ValidationErrorException;
use Apps\Core_MobileApi\Api\Form\Type\CountryStateType;
use Apps\Core_MobileApi\Api\Form\Type\FormTypeInterface;
use Apps\Core_MobileApi\Api\Form\Type\GeneralType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Type\MembershipPackageType;
use Apps\Core_MobileApi\Api\Form\Type\PrivacyType;
use Apps\Core_MobileApi\Api\Form\Validator\NumberRangeValidator;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;
use Apps\Core_MobileApi\Api\Form\Validator\TypeValidator;
use Apps\Core_MobileApi\Api\Form\Validator\ValidateInterface;
use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Apps\Core_MobileApi\Service\Helper\PsrRequestHelper;
use Apps\Core_MobileApi\Service\SubscriptionApi;
use Phpfox;

abstract class GeneralForm
{
    /**
     * @var GeneralType[]
     */
    protected $sections = [];
    protected $fields = [];
    protected $title;
    protected $description;
    protected $action;
    protected $method;

    protected $isBuild = false;
    protected $isPost = true;

    /**
     * @var array assigned array of field's values
     */
    protected $data;

    /**
     * @var SettingInterface
     */
    protected $setting;

    /**
     * @var LocalizationInterface
     */
    protected $local;

    /**
     * @var UserPrivacyInterface
     */
    protected $privacy;

    protected $invalidFields;

    /**
     * @var ApiRequestInterface
     */
    protected $request;

    /**
     * @var array Current values cached after called getValues method
     */
    protected $values;


    /**
     * Override build form to generate form
     * @return mixed
     * @internal param array $data
     */
    abstract public function buildForm();

    /**
     * This function is called on isValid, getStructure, getValues method
     * self::buildForm() method must be called before this method
     */
    public function buildValues()
    {
        if (empty($this->data)) {
            return;
        }
        foreach ($this->fields as $name => $field) {
            if ($field instanceof TransformerInterface) {
                // Get Edit form (this case has no field in data)
                if ((!isset($this->data[$name]) && !$this->isPost) || !$this->isPost) {
                    $field->setValue($field->reverseTransform($this->data), $this->isPost);
                } else { // Process POST data
                    $field->setValue(isset($this->data[$name]) ? $this->data[$name] : null, $this->isPost);
                }

            } else {
                $field->setValue(isset($this->data[$name]) ? $this->data[$name] : null, $this->isPost);
            }
        }
    }

    /**
     * Create New Form object
     *
     * @param array                                       $options
     * @param null|array|ApiRequestInterface|ResourceBase $data
     * @param LocalizationInterface|null                  $local
     * @param UserPrivacyInterface|null                   $privacy
     * @param SettingInterface|null                       $setting
     *
     * @return static
     */
    public static function createForm($options = null,
                                      $data = null,
                                      LocalizationInterface $local = null,
                                      SettingInterface $setting = null,
                                      UserPrivacyInterface $privacy = null)
    {
        $form = (new static());
        $form->setSetting($setting);
        $form->setLocal($local);
        $form->setPrivacy($privacy);
        if (!empty($options['title'])) {
            $form->setTitle($options['title']);
        }
        if (!empty($options['description'])) {
            $form->setDescription($options['description']);
        }

        if (!empty($options['action'])) {
            $form->setAction($options['action']);
        }

        if (!empty($options['method'])) {
            $form->setMethod($options['method']);
        }

        if (!empty($data)) {
            if ($data instanceof ApiRequestInterface) {
                $form->assignValues($data->getRequests());
            } else if ($data instanceof ResourceBase) {
                $form->assignValues($data->toArray());
            } else {
                $form->assignValues($data);
            }
        }

        return $form;
    }

    /**
     * Validate form value
     * @return bool
     */
    public function isValid()
    {
        if (!$this->isBuild) {
            $this->buildForm();
            $this->buildValues();
        }
        $bValid = true;
        foreach ($this->fields as $field) {
            if (!$field->isValid()) {
                $bValid = false;
                $this->invalidFields[$field->getName()] = $field->getErrorMessage();
            }
        }
        return $bValid;
    }

    public function getInvalidFields()
    {
        return $this->invalidFields;
    }

    public function setInvalidField($fieldName, $message)
    {
        return $this->invalidFields[$fieldName] = $message;
    }

    /**
     * Get Form field value
     * @return array
     */
    public function getValues()
    {
        if (!empty($this->values)) {
            return $this->values;
        }
        if (!$this->isBuild) {
            $this->buildForm();
            $this->buildValues();
        }
        $values = [];

        foreach ($this->fields as $field) {
            $values[$field->getName()] = $field->getValue();
            if ($field instanceof TransformerInterface) {
                ArrayUtility::append($values, $field->transform($field->getValue()));
            }
        }

        // Cache the value result
        $this->values = $values;
        return $values;
    }

    /**
     * From support group values base on prefix
     *
     * @param string $group prefix
     *
     * @return array return all field values as array has name format "{$group}_"
     */
    public function getGroupValues($group)
    {
        $values = $this->getValues();
        return self::groupArrayPrefix($group, $values);
    }

    /**
     * Utility function for grouping parameters with prefix
     *
     * @param $prefix
     * @param $values
     *
     * @return array
     */
    protected static function groupArrayPrefix($prefix, $values)
    {
        if (empty($values) || !is_array($values)) {
            return [];
        }
        $result = [];
        foreach ($values as $key => $value) {
            if (strpos($key, $prefix . "_") === 0) {
                $result[str_replace($prefix . "_", "", $key)] = $value;
            }
        }

        return $result;
    }

    /**
     * Add a new Section to the form
     *
     * @param $name
     * @param $label
     * @param $description
     *
     * @return $this
     * @throws ValidationErrorException
     */
    public function addSection($name, $label, $description = null)
    {
        if (empty($name)) {
            throw new ValidationErrorException(_p("Please specified `name` option"));
        }
        $this->sections[$name]['label'] = $label;
        $this->sections[$name]['fields'] = [];
        $this->sections[$name]['description'] = $description;

        return $this;
    }

    /**
     * Add a new Field to the form. This function MUST call when build form
     *
     * @param string                   $name       Name of the control
     * @param string                   $type       Class name of FormType
     * @param array                    $options
     * @param null|ValidateInterface[] $validators list of validator
     * @param string                   $section
     *
     * @return $this
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addField($name, $type, $options = [], $validators = null, $section = null)
    {
        if (empty($name) || empty($type)) {
            throw new ValidationErrorException(_p("Please specified `name`, `type` and `label` option"));
        }
        $type = $this->createField($type);
        $type->setName($name);
        $type->setLocal($this->getLocal());
        $type->setSetting($this->setting);
        $type->setAttrs($options);
        $type->setValidators($validators);
        if ($section) {
            $this->sections[$section]['fields'][$name] = $type;
            $type->setSection($section);
        }
        $this->fields[$name] = $type;

        return $this;
    }

    /**
     * Quick add privacy select
     *
     * @param array $options
     * @param null  $section
     * @param int   $defaultValue
     *
     * @return $this
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addPrivacyField($options = [], $section = null, $defaultValue = PrivacyType::EVERYONE)
    {
        $defaultOptions = [
            'options'       => (new PrivacyType())->getDefaultPrivacy(),
            'label'         => 'privacy',
            'multiple'      => false,
            'value_default' => $defaultValue
        ];
        if (empty($options['disable_custom']) && Phpfox::isModule('friend')) {
            $defaultOptions['options'][] = [
                'label' => $this->local->translate('custom'),
                'value' => PrivacyType::CUSTOM
            ];
        }
        if (!empty($options)) {
            $defaultOptions = array_merge($defaultOptions, $options);
        }
        $this->addField('privacy', PrivacyType::class, $defaultOptions, null, $section);
        return $this;
    }

    /**
     * @param bool   $required
     * @param string $label
     * @param null   $section
     * @param bool   $anyWhere
     *
     * @return $this
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addCountryField($required = false, $label = 'country', $section = null, $anyWhere = false)
    {
        $countries = $this->local->getAllCountry();
        $allCountries = [];
        $allChild = [];
        if ($anyWhere) {
            $allCountries[] = [
                'value' => '',
                'label' => $this->local->translate('anywhere'),
            ];
        }
        if (!empty($countries)) {
            foreach ($countries as $ios => $country) {
                if (Phpfox::isPhrase('translate_country_iso_' . strtolower($ios))) {
                    $country = $this->local->translate('translate_country_iso_' . strtolower($ios));
                }
                $allCountries[] = [
                    'value' => $ios,
                    'label' => str_replace('&#039;', '\'', Phpfox::getService(ParseInterface::class)->cleanOutput($country))
                ];
                $childByIos = $this->local->getAllState($ios);
                if (!empty($childByIos)) {
                    foreach ($childByIos as $childId => $child) {
                        if (Phpfox::isPhrase('translate_country_child_' . strtolower($childId))) {
                            $child = $this->local->translate('translate_country_child_' . strtolower($childId));
                        }
                        $allChild[$ios][] = [
                            'value' => (int)$childId,
                            'label' => str_replace('&#039;', '\'', Phpfox::getService(ParseInterface::class)->cleanOutput($child))
                        ];
                    }
                }
            }
        }
        //Get default country of user
        $countryIOS = Phpfox::getUserBy('country_iso') ? Phpfox::getUserBy('country_iso') : ($anyWhere ? '' : null);
        $countryChild = (int)Phpfox::getUserBy('country_child_id');

        $this->addField('country_state', CountryStateType::class, [
            'label'         => $label,
            'options'       => $allCountries,
            'suboptions'    => $allChild,
            'required'      => $required,
            'value_default' => [$countryIOS, $countryChild],
        ], $required ? [new RequiredValidator()] : null, $section);
        return $this;

    }

    /**
     * @param       $name
     * @param       $type
     * @param null  $validation
     * @param array $options
     * @param null  $section
     *
     * @return $this
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addMultipleLanguageFields($name, $type, $validation = null, $options = [], $section = null)
    {
        $languages = $this->local->getAllLanguage();
        $initTitle = isset($options['label']) ? $options['label'] : '';
        if (!empty($languages)) {
            foreach ($languages as $language) {
                $id = $language['language_id'];
                if ($initTitle) {
                    $options['label'] = $this->local->translate($initTitle) . ' ' . $this->local->translate('in_language', ['language' => $language['title']]);
                }
                $this->addField("{$name}_{$id}", $type, $options, $validation, $section);
            }
        }
        return $this;
    }

    /**
     * Quick add `module_id` and `item_id` fields
     *
     * @param array  $options
     * @param string $module
     * @param string $item
     * @param null   $section
     *
     * @return $this
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addModuleFields($options = [], $module = 'module_id', $item = 'item_id', $section = null)
    {
        $this->addField($module, HiddenType::class,
            (isset($options['module_value']) ? ['value_default' => $options['module_value']] : null),
            [new TypeValidator(TypeValidator::IS_STRING)], $section)
            ->addField($item, HiddenType::class,
                (isset($options['item_value']) ? ['value_default' => $options['item_value']] : null),
                [new NumberRangeValidator(0)], $section);
        return $this;
    }

    /**
     * @param array $options
     * @param null  $section
     *
     * @return $this
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addAttachmentField($options = [], $section = null)
    {
        $this->addField('attachment_id',
            HiddenType::class, $options,
            [new NumberRangeValidator(1)], $section);
        return $this;
    }

    /**
     * @param bool   $required
     * @param string $label
     * @param null   $section
     * @param bool   $forRegister
     * @param array  $extraOptions
     *
     * @return $this
     * @throws ValidationErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    public function addMembershipPackageField($required = false, $label = 'package', $section = null, $forRegister = false, $extraOptions = [])
    {
        $packages = Phpfox::getService('subscribe')->getPackages($forRegister);

        (($sPlugin = \Phpfox_Plugin::get('mobile.api_form_general_form_membership_field_start')) ? eval($sPlugin) : false);

        $valueDefault = 0;
        $currentMembership = [];
        if (!$forRegister) {
            $valueDefault = Phpfox::getService('subscribe.purchase')->getSubscriptionsIdPurchasedByUser(Phpfox::getUserId());
            if (!empty($valueDefault) && isset($valueDefault[0])) {
                $valueDefault = (int)$valueDefault[0];
                $currentMembership = (new SubscriptionApi())->getMembershipDetail($valueDefault);
            }
        }
        if (count($packages)) {
            $parsedPackages = [];
            foreach ($packages as $package) {
                $cost = unserialize($package['cost']);
                $recurringCost = unserialize($package['recurring_cost']);
                $defaultCurrency = $this->local->getDefaultCurrency();
                $defaultCost = isset($cost[$defaultCurrency]) ? $cost[$defaultCurrency] : null;
                $defaultRecurringCost = isset($recurringCost[$defaultCurrency]) ? $recurringCost[$defaultCurrency] : null;
                $thumbnailSizes = [120];
                $images = null;

                if (!empty($package['image_path'])) {
                    $images = Image::createFrom([
                        'file'      => $package['image_path'],
                        'server_id' => $package['server_id'],
                        'path'      => 'subscribe.url_image'
                    ], $thumbnailSizes, false)->toArray();
                }

                $recurringInfo = html_entity_decode(Phpfox::getService('subscribe')->getPeriodPhrase($package['recurring_period'], $defaultRecurringCost, $defaultCost, $defaultCurrency), ENT_QUOTES);
                $recurringInfo = preg_replace('/\((.+)?\)/', '$1', $recurringInfo);
                $option = [
                    'value'          => (int)$package['package_id'],
                    'label'          => $this->local->translate($package['title']),
                    'description'    => $this->local->translate($package['description']),
                    'is_free'        => !!$package['is_free'] || !(float)$defaultCost,
                    'is_recurring'   => !!$package['recurring_period'],
                    'init_cost_info' => html_entity_decode($this->local->getCurrency($defaultCost, $defaultCurrency), ENT_QUOTES),
                    'recurring_info' => ucfirst(!!$package['recurring_period'] ? $recurringInfo : $this->local->translate('one_time')),
                    'image'          => !empty($images['120']) ? $images['120'] : Phpfox::getParam('subscribe.app_url') . Phpfox::getParam('subscribe.default_photo_package'),
                    'activate_value' => $valueDefault
                ];

                (($sPlugin = \Phpfox_Plugin::get('mobile.api_form_general_membership_field_processing')) ? eval($sPlugin) : false);

                $parsedPackages[] = $option;
                if (!empty($currentMembership) && $option['value'] == $valueDefault) {
                    $currentMembership = array_merge($currentMembership, $option);
                }
            }
            if ($section !== null) {
                $this->addSection($section, $section);
            }

            (($sPlugin = \Phpfox_Plugin::get('mobile.api_form_general_form_membership_field_end')) ? eval($sPlugin) : false);

            $this->addField('package_id', MembershipPackageType::class, array_merge([
                'label'               => $label,
                'options'             => $parsedPackages,
                'required'            => $required,
                'value_default'       => $valueDefault,
                'is_register'         => !Phpfox::getUserId(),
                'activate_membership' => $currentMembership,
                'activate_package_id' => !empty($currentMembership) ? $currentMembership['value'] : 0
            ], $extraOptions), $required ? [new RequiredValidator()] : null, $section)
                ->addField('current_package_id', HiddenType::class, [
                    'value' => $valueDefault && !empty($currentMembership) ? $valueDefault : 0
                ]);
        }
        return $this;

    }

    /**
     * Remote an existed field
     *
     * @param string $name unique name
     * @param string $section
     *
     * @return $this
     */
    public function removeField($name, $section = null)
    {
        if ($section) {
            unset($this->sections[$section]['fields'][$name]);
        }
        unset($this->fields[$name]);
        return $this;
    }

    /**
     * Generate form Structure
     * @return array
     */
    public function getFormStructure()
    {
        $this->isPost = false;

        if (!$this->isBuild) {
            $this->buildForm();
            $this->buildValues();
        }
        $schema = [
            'title'       => $this->local->translate($this->title),
            'description' => $this->local->translate($this->description),
            'action'      => $this->getAction(),
            'method'      => ($this->method ? $this->method : "post")
        ];

        /**
         * @var GeneralType $section
         * @var GeneralType $field
         */
        foreach ($this->sections as $sectionName => $section) {
            $schema['sections'][$sectionName]['label'] = $this->local->translate($section['label']);
            if (!empty($section['description'])) {
                $schema['sections'][$sectionName]['description'] = $this->local->translate($section['description']);
            }
            if (!empty($section['fields'])) {
                $fields = [];
                foreach ($section['fields'] as $name => $field) {
                    $fields[$name] = $field->getStructure($this->local);
                }
                $schema['sections'][$sectionName]['fields'] = $fields;
            }
        }

        /** @var GeneralType $field */
        foreach ($this->fields as $name => $field) {
            if ($field->getSection() == null) {
                $schema['fields'][$name] = $field->getStructure($this->local);
            }
        }
        if ($this->request && $this->request->isGet() && $this->request->get('help')) {
            $schema['help'] = $this->getHelpInformation();
        }
        return $schema;
    }


    /**
     * @param $type
     *
     * @return GeneralType|FormTypeInterface
     */
    public function createField($type)
    {
        $formType = new $type();
        return $formType;
    }

    /**
     * Assign data to the form
     *
     * @param array|ResourceBase $data
     *
     * @return $this
     */
    public function assignValues($data)
    {
        if ($data instanceof ResourceBase) {
            $this->data = $data->toArray();
        } else {
            $this->data = $data;
        }
        return $this;
    }

    /**
     * Handle form or API submission
     *
     * @param $request PsrRequestHelper|ApiRequestInterface
     *
     * @return $this
     */
    public function handleRequest($request)
    {
        $this->assignValues($request->getRequests());
        return $this;
    }

    /**
     * @param mixed $title
     *
     * @return GeneralForm
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $description
     *
     * @return GeneralForm
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $action
     *
     * @return GeneralForm
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param SettingInterface $setting
     *
     * @return GeneralForm
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;
        return $this;
    }

    /**
     * @param LocalizationInterface $local
     *
     * @return GeneralForm
     */
    public function setLocal($local)
    {
        $this->local = $local;
        return $this;
    }

    /**
     * @return LocalizationInterface
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * @return UserPrivacyInterface
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * @param UserPrivacyInterface $privacy
     */
    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
    }


    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Check field is existed
     *
     * @param $name
     *
     * @return bool
     */
    public function isField($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * @param $name
     *
     * @return GeneralType
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }

    /**
     * @param ApiRequestInterface $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * Add help=1 into api url to get Help information for request and response
     * @return array
     */
    protected function getHelpInformation()
    {
        $request = [];
        foreach ($this->fields as $name => $field) {
            $request[$name] = ($field->getValueDefault() ? $field->getValueDefault() : $field->getMetaValueFormat());
        }
        return [
            'api_endpoint'   => $this->getAction(),
            'method'         => $this->getMethod(),
            'sample_request' => $request
        ];
    }

    protected function getSizeLimit($value, $isMb = false)
    {
        if ($value == 0) {
            //If setting is unlimited > Should check limit of server
            $limit = [
                Phpfox::getLib('file')->getSizeToMb(ini_get('upload_max_filesize')),
                Phpfox::getLib('file')->getSizeToMb(ini_get('post_max_size'))
            ];
            return min($limit) * 1024;
        }
        if (!$isMb) {
            $value = $value / 1024;
        }
        return Phpfox::getLib('file')->getLimit($value, true) * 1024;
    }

    public function getAssociativeArrayData($key)
    {
        return $this->getDataBy($key);
    }

    protected function getDataBy($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : [];
    }
}