<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 20/6/18
 * Time: 4:29 PM
 */

namespace Apps\Core_MobileApi\Api\Form\User;


use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\BirthdayType;
use Apps\Core_MobileApi\Api\Form\Type\ChoiceType;
use Apps\Core_MobileApi\Api\Form\Type\CustomGendersType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Type\MultiChoiceType;
use Apps\Core_MobileApi\Api\Form\Type\RadioType;
use Apps\Core_MobileApi\Api\Form\Type\RelationshipPickerType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TextareaType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;

class EditProfileForm extends GeneralForm
{

    private $defaultGender = 1;
    protected $userId;
    protected $userGroupId;
    protected $aRelation;

    public function __construct()
    {
        $this->userId = \Phpfox::getUserId();
        $this->userGroupId = \Phpfox::getUserBy('user_group_id');
    }

    /**
     * Override build form to generate form
     *
     * @return mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     * @throws \Apps\Core_MobileApi\Api\Exception\ValidationErrorException
     */
    public function buildForm()
    {
        $sectionName = 'basic';

        $this->addSection($sectionName, 'basic_information')
            ->addCountryField(!!$this->setting->getAppSetting('user.require_basic_field'), 'country', $sectionName, false)
            ->addField('city_location', TextType::class, [
                'label'       => 'city',
                'inline'      => true,
                'placeholder' => 'city_name'
            ], [], $sectionName)
            ->addField('postal_code', TextType::class, [
                'label'       => 'postal_code',
                'inline'      => true,
                'placeholder' => '- - - - - -'
            ], [], $sectionName)
            ->addField('gender', RadioType::class, [
                'options'       => $this->genderOptions(),
                'label'         => 'i_am',
                'value_default' => $this->defaultGender,
                'required'      => !!$this->setting->getAppSetting('user.require_basic_field')
            ], [], $sectionName)->addField('custom_gender', CustomGendersType::class, [
                'label'        => '',
                'hidden_by'    => '!gender',
                'hidden_value' => ['127'],
                'description'  => 'separate_multiple_genders_with_commas'
            ], [], $sectionName)
            ->addField('birthday', BirthdayType::class, [
                'label'       => 'birthday',
                'minDate'     => $this->setting->getAppSetting('user.date_of_birth_start') . '-1-1',
                'maxDate'     => $this->setting->getAppSetting('user.date_of_birth_end') . '-12-31',
                'inline'      => true,
                'placeholder' => 'YYYY-MM-DD',
                'required'    => !!$this->setting->getAppSetting('user.require_basic_field')
            ], [], $sectionName)
            ->addField('previous_relation_type', HiddenType::class, [
                'value' => $this->getRelationshipValue()
            ], [], $sectionName)
            ->addField('previous_relation_with', HiddenType::class, [
                'value' => $this->getRelationshipWithValue()
            ], [], $sectionName);

        if ($this->setting->getAppSetting('user.enable_relationship_status')) {
            $this->addField('relation', ChoiceType::class, [
                'label'           => 'custom_relationship_status',
                'options'         => $this->getRelationShipOptions(),
                'value'           => $this->getRelationshipValue(),
                'depend_field'    => 'relation_with',
                'disable_uncheck' => true
            ], [], $sectionName);
            $this->addField('relation_with', RelationshipPickerType::class, [
                'label'        => '__with',
                'item_id'      => $this->userId,
                'item_type'    => 'user',
                'multiple'     => false,
                'value'        => $this->getRelationshipWithValue(),
                'hidden_by'    => 'relation',
                'is_relation'  => true,
                'description'  => $this->getRelationshipWithDescription(),
                'hidden_value' => [null, '0', 1, 2, 5, 7, 8, 9]
            ], [], $sectionName);
        }
        if ($this->userId != \Phpfox::getUserId()) {
            $this->addField('user_id', HiddenType::class, [
                'value' => $this->userId
            ], [], $sectionName);
        }

        $sectionName = 'about';
        $this->addSection($sectionName, 'about_me');
        $this->buildCustomFields($sectionName);

        $this->addField('submit', SubmitType::class, [
            'label' => 'update'
        ]);

    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param string $userGroupId
     */
    public function setUserGroupId($userGroupId)
    {
        $this->userGroupId = $userGroupId;
    }


    private function genderOptions()
    {
        $genders = \Phpfox::getService('core')->getGenders();
        $options = [];
        $i = 0;
        if (!$this->setting->getAppSetting('user.require_basic_field')) {
            $options[] = [
                'value' => 0,
                'label' => ''
            ];
        }
        foreach ($genders as $key => $gender) {
            if ($i == 0) {
                $this->defaultGender = $key;
            }
            $options[] = [
                'value' => (string)$key,
                'label' => $this->local->translate($gender)
            ];
            $i++;
        }

        if ($this->setting->getUserSetting('user.can_add_custom_gender') || $this->setting->getUserSetting('user.can_add_custom_gender') === null) {
            $options[] = [
                'value' => '127',
                'label' => $this->local->translate('others_upper')
            ];
        }
        return $options;
    }

    private function getRelationShipOptions()
    {
        $relations = \Phpfox::getService('custom.relation')->getAll();
        $options = [];
        foreach ($relations as $relation) {
            $label = $this->getLocal()->translate($relation['phrase_var_name']);
            if (empty(trim($label))) {
                $label = $this->getLocal()->translate('unknown_status');
            }
            $options[] = [
                'value' => (int)$relation['relation_id'],
                'label' => $label
            ];
        }
        return $options;
    }

    private function getRelationshipValue()
    {
        if (!$this->aRelation) {
            $this->aRelation = \Phpfox::getService('custom.relation')->getLatestForUser($this->userId, null, true);
        }
        return (isset($this->aRelation['relation_id']) ? (int)$this->aRelation['relation_id'] : '0');
    }

    private function getRelationshipWithValue()
    {
        if (!$this->aRelation) {
            $this->aRelation = \Phpfox::getService('custom.relation')->getLatestForUser($this->userId, null, true);
        }
        return (isset($this->aRelation['with_user_id']) ? (int)$this->aRelation['with_user_id'] : null);
    }

    private function getRelationshipWithDescription()
    {
        if (!$this->aRelation) {
            $this->aRelation = \Phpfox::getService('custom.relation')->getLatestForUser($this->userId, null, true);
        }
        $isPending = isset($this->aRelation['with_user']['status_id']) && $this->aRelation['with_user']['status_id'] == 1;
        if ($isPending) {
            return $this->getLocal()->translate('pending_confirmation');
        }
        return '';
    }

    /**
     * @param null $sectionName
     *
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    private function buildCustomFields($sectionName = null)
    {
        if ($this->setting->getUserSetting('custom.can_edit_own_custom_field') == false) {
            return;
        }

        $customFields = \Phpfox::getService('custom')->getForEdit([
            'user_main',
            'user_panel',
            'profile_panel'
        ], $this->userId, $this->userGroupId, false, $this->userId);

        foreach ($customFields as $field) {
            if ($field['is_active']) {
                $require = (bool)$field['is_required'];
                $fieldName = "custom_" . $field['field_id'];
                switch ($field['var_type']) {
                    case "textarea":
                        $this->addField($fieldName, TextareaType::class, [
                            'label'       => $field['phrase_var_name'],
                            'placeholder' => 'type_something_dot',
                            'required'    => $require,
                            'value'       => $field['value']
                        ], [], $sectionName);
                        break;
                    case "text":
                        $this->addField($fieldName, TextType::class, [
                            'label'       => $field['phrase_var_name'],
                            'placeholder' => 'type_something_dot',
                            'required'    => $require,
                            'value'       => $field['value'],
                        ], [], $sectionName);
                        break;
                    case "select":
                        $options = [];
                        foreach ($field['options'] as $value => $option) {
                            $options[] = [
                                'label' => $this->getLocal()->translate($option['value']),
                                'value' => (int)$value
                            ];
                        }
                        $this->addField($fieldName, ChoiceType::class, [
                            'options'  => $options,
                            'label'    => $field['phrase_var_name'],
                            'required' => $require,
                            'value'    => !empty($field['customValue']) ? (int)$field['customValue'] : null
                        ], [], $sectionName);
                        break;
                    case "multiselect":
                    case "checkbox":
                        $options = [];
                        foreach ($field['options'] as $value => $option) {
                            $options[] = [
                                'label' => $this->getLocal()->translate($option['value']),
                                'value' => (string)$value
                            ];
                        }
                        $this->addField($fieldName, MultiChoiceType::class, [
                            'options'  => $options,
                            'label'    => $field['phrase_var_name'],
                            'required' => $require,
                            'value'    => !empty($field['customValue']) && !empty($field['customValue'][0]) ? $field['customValue'] : []
                        ], [], $sectionName);
                        break;
                    case "radio":
                        $options = [];
                        foreach ($field['options'] as $value => $option) {
                            $options[] = [
                                'label' => $this->getLocal()->translate($option['value']),
                                'value' => (int)$value
                            ];
                        }
                        $this->addField($fieldName, RadioType::class, [
                            'options'  => $options,
                            'label'    => $field['phrase_var_name'],
                            'required' => $require,
                            'value'    => !empty($field['customValue']) ? (int)$field['customValue'] : null
                        ], [], $sectionName);
                        break;
                }
            }
        }
    }

    public function isValid()
    {
        if (!parent::isValid()) {
            return false;
        }
        $passed = true;
        $gender = $this->getField('gender')->getValue();
        $customGender = $this->getField('custom_gender')->getValue();
        $custom = explode(',', $customGender);
        if ($gender == '127' && (empty($customGender) || (count($custom) == 1 && !$custom[0]))) {
            $this->setInvalidField('custom_gender', $this->getLocal()->translate('please_type_at_least_one_custom_gender'));
            $passed = false;
        }

        return $passed;
    }
}