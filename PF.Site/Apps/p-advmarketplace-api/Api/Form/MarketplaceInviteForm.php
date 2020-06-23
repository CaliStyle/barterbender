<?php


namespace Apps\P_AdvMarketplaceAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\FriendPickerType;
use Apps\Core_MobileApi\Api\Form\Type\RadioType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Validator\NumberRangeValidator;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;

class MarketplaceInviteForm extends GeneralForm
{

    protected $action = "mobile/advancedmarketplace-invite";

    protected $editing = false;

    protected $itemId;

    function buildForm($options = null, $data = [])
    {
        $this
            ->addField('user_ids', FriendPickerType::class, [
                'label' => 'invite_friends',
                'item_id' => $this->itemId,
                'item_type' => 'advancedmarketplace',
                'description' => $this->local->translate('make_sure_your_listings_in_a_published_version_before_sending_any_invitations_to_your_friends'),
                'help_position' => 'prepend'
            ])
            ->addField('listing_id', HiddenType::class, [
                'value' => $this->itemId,
                'required' => true
            ], [new RequiredValidator()])
            ->addField('emails', TextType::class, [
                'label' => 'invite_people_via_email',
                'autoCapitalize' => 'none'
            ])
            ->addField('personal_message', TextType::class, [
                'label' => 'add_a_personal_message',
                'placeholder' => $this->local->translate('enter_your_message')
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