<?php


namespace Apps\Core_MobileApi\Api\Form\Group;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\HierarchyType;
use Apps\Core_MobileApi\Api\Form\Type\RadioType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;
use Apps\Core_MobileApi\Api\Form\Validator\StringLengthValidator;
use Apps\Core_MobileApi\Api\Form\Validator\TypeValidator;

class GroupProfileForm extends GeneralForm
{

    protected $action = "group-profile";
    protected $editing = false;
    protected $categories;

    /**
     * @param null  $options
     * @param array $data
     *
     * @return mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    function buildForm($options = null, $data = [])
    {
        $this
            ->addField('title', TextType::class, [
                'required' => true,
                'order'    => 1,
            ], [new StringLengthValidator(1, 64)])
            ->addField('reg_method', RadioType::class, [
                'label'         => 'Groups privacy',
                'options'       => [
                    [
                        'value' => 0,
                        'label' => $this->local->translate('public_group')
                    ],
                    [
                        'value' => 1,
                        'label' => $this->local->translate('closed_group')
                    ],
                    [
                        'value' => 2,
                        'label' => $this->local->translate('secret_group')
                    ],
                ],
                'order'         => 2,
                'value_default' => 0,
                'required'      => true,
            ], [new RequiredValidator()])
            ->addField('type_category', HierarchyType::class, [
                'rawData'    => $this->categories,
                'multiple'   => false,
                'field_maps' => [
                    'field_type' => 'string',
                    'field_sub'  => 'categories'
                ],
                'order'      => 3,
                'required'   => true,
                'label'      => 'category'
            ], [new RequiredValidator(), new TypeValidator(TypeValidator::IS_ARRAY_STRING)])
            ->addField('submit', SubmitType::class, [
                'label' => 'update',
            ]);
    }

    public function setEditing($edit)
    {
        $this->editing = $edit;
    }

    public function getEditing()
    {
        return $this->editing;
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
}