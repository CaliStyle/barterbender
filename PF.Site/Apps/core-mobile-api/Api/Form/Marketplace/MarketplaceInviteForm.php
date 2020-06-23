<?php


namespace Apps\Core_MobileApi\Api\Form\Marketplace;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\FriendPickerType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;

class MarketplaceInviteForm extends GeneralForm
{
    protected $action = "mobile/marketplace-invite";
    protected $editing = false;
    protected $itemId;

    /**
     * @param null  $options
     * @param array $data
     *
     * @return mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    function buildForm($options = null, $data = [])
    {
        $this->addField('user_ids', FriendPickerType::class, [
            'label'     => 'invite_friends',
            'item_id'   => $this->itemId,
            'item_type' => 'marketplace'
        ])
            ->addField('listing_id', HiddenType::class, [
                'value'    => $this->itemId,
                'required' => true
            ], [new RequiredValidator()])
            ->addField('emails', TextType::class, [
                'label'          => 'invite_people_via_email',
                'autoCapitalize' => 'none',
                'placeholder'    => 'enter_email_address'
            ])
            ->addField('personal_message', TextType::class, [
                'label'       => 'add_a_personal_message',
                'placeholder' => 'enter_your_message'
            ]);
        $this->addField('submit', SubmitType::class, [
            'label' => 'save'
        ]);
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }


    public function setEditing($edit)
    {
        $this->editing = $edit;
    }

    public function getEditing()
    {
        return $this->editing;
    }

}