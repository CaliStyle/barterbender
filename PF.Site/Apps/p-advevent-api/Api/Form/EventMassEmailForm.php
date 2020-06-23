<?php


namespace Apps\P_AdvEventAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Type\TextareaType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;
use Phpfox;

class EventMassEmailForm extends GeneralForm
{

    protected $action = "mobile/fevent-invite";

    protected $editing = false;

    protected $itemId;

    function buildForm($options = null, $data = [])
    {
        $sectionName = 'all';
        $this
            ->addSection($sectionName, ' ',
                $this->local->translate('send_out_an_email_to_all_the_guests_that_are_joining_this_event'))
            ->addField('event_id', HiddenType::class, [
                'value' => $this->itemId,
                'required' => true
            ], [new RequiredValidator()], $sectionName)
            ->addField('subject', TextType::class, [
                'label' => 'subject',
            ], [new RequiredValidator()], $sectionName)
            ->addField('text', TextareaType::class, [
                'label' => 'text',
            ], [new RequiredValidator()], $sectionName);
    }

    /**
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    public function getEditing()
    {
        return $this->editing;
    }
}