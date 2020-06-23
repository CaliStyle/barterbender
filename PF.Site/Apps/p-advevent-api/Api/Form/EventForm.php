<?php


namespace Apps\P_AdvEventAPI\Api\Form;


use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\AttachmentType;
use Apps\Core_MobileApi\Api\Form\Type\ChoiceType;
use Apps\Core_MobileApi\Api\Form\Type\HierarchyType;
use Apps\Core_MobileApi\Api\Form\Type\DateTimeType;
use Apps\Core_MobileApi\Api\Form\Type\DateType;
use Apps\Core_MobileApi\Api\Form\Type\TimeType;
use Apps\Core_MobileApi\Api\Form\Type\FileType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TextareaType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Type\IntegerType;
use Apps\Core_MobileApi\Api\Form\Validator\RequiredValidator;
use Apps\Core_MobileApi\Api\Form\Validator\StringLengthValidator;
use Apps\Core_MobileApi\Api\Form\Validator\TypeValidator;
use Apps\Core_MobileApi\Api\Form\Validator\NumberRangeValidator;
use Phpfox;

class EventForm extends GeneralForm
{
    protected $categories;

    protected $countries;

    protected $tags;

    protected $action = "fevent";

    protected $editing = false;
    protected $isrepeat;

    public function setEditing($editing)
    {
        $this->editing = $editing ? true : false;
    }

    public function setIsrepeat($isrepeat)
    {
        $this->isrepeat = $isrepeat == '-1' ? false : true;
    }

    function buildForm($options = null, $data = [])
    {
        $values = $this->data;
        $isRepeatEvent = isset($this->isrepeat) ? $this->isrepeat : (isset($values['isrepeat']) && $values['isrepeat'] > -1);
        $allowChangeDate = $this->setting->getAppSetting('fevent.allow_change_date_recurrent_event');
        $allowChangeTime = $this->setting->getAppSetting('fevent.allow_change_time_recurrent_event');

        $sectionName = 'basic';
        $this->addSection($sectionName, 'basic_info');

        if ($this->editing) {
            if ($isRepeatEvent) {
                $this->addField('ynfevent_editconfirmboxoption_value', ChoiceType::class,
                    [
                        'label' => 'apply_edits_for_cap',
                        'required' => true,
                        'value_default' => 'only_this_event',
                        'options' => [
                            [
                                'label' => $this->local->translate('only_this_event'),
                                'value' => 'only_this_event',
                            ],
                            [
                                'label' => $this->local->translate('following_events'),
                                'value' => 'following_events',
                            ],
                            [
                                'label' => $this->local->translate('all_events_uppercase'),
                                'value' => 'all_events_uppercase',
                            ],
                        ]
                    ], [new RequiredValidator()], $sectionName);
            } else {
                $this->addField('ynfevent_editconfirmboxoption_value', HiddenType::class, [
                    'value' => 'only_this_event',
                ], null, $sectionName);
            }
        }

        $this
            ->addField('title', TextType::class, [
                'label' => 'event_name',
                'placeholder' => $this->local->translate('fill_title_for_event') . '. ' . $this->local->translate('maximum_number_characters', ['number' => 100]),
                'required' => true
            ], [new StringLengthValidator(1, 100)], $sectionName)
            ->addField('categories', HierarchyType::class, [
                'label' => 'categories',
                'rawData' => $this->categories,
                'multiple' => false
            ], [new TypeValidator(TypeValidator::IS_ARRAY_NUMERIC)], $sectionName)
            ->addField('text', TextareaType::class, [
                'label' => 'description',
                'placeholder' => 'add_description_to_event',
            ], null, $sectionName)
            ->addField('attachment', AttachmentType::class, [
                'label' => 'attachment',
                'item_type' => "event",
                'item_id' => (isset($this->data['id']) ? $this->data['id'] : null),
                'current_attachments' => $this->getAttachments()
            ], [new TypeValidator(TypeValidator::IS_ARRAY_NUMERIC)], $sectionName);

        $sectionName = 'ticket';

        $this
            ->addSection($sectionName, ' ')
            ->addField('ticket_type', ChoiceType::class,
                $this->getTicketTypeOptions(), [new RequiredValidator()], $sectionName)
            ->addField('ticket_price', TextType::class, [
                'label' => 'ticket_price',
                'placeholder' => 'this_field_apply_for_paid_ticket_only'
            ], null, $sectionName)
            ->addField('ticket_url', TextType::class, [
                'label' => 'get_ticket_on_website',
                'placeholder' => 'link_to_ticket_info_http'
            ], null, $sectionName);

        $sectionName = 'additional_info';
        $this->addSection($sectionName, 'additional_info');

//        if ($this->editing && $isRepeatEvent && (!$allowChangeDate || !$allowChangeTime)) {
//            if ($allowChangeDate) {
//                $this->addField('start_time_date', DateType::class, [
//                    'label' => 'start_date',
//                    'placeholder' => 'select_start_date',
//                    'required' => true
//                ], [new RequiredValidator()], $sectionName);
//            } else {
//                $this
//                    ->addField('start_time_date_text', TextType::class, [
//                        'label' => 'start_date',
//                        'required' => true,
//                        'editable' => false,
//                    ], null, $sectionName)
//                    ->addField('start_time_date', HiddenType::class, [
//                        'value' => $values['start_time_date'],
//                    ], null, $sectionName);
//            }
//
//            if ($allowChangeTime) {
//                $this->addField('start_time_time', TimeType::class, [
//                    'label' => 'start_time',
//                    'placeholder' => 'select_start_time',
//                    'required' => true
//                ], [new RequiredValidator()], $sectionName);
//            } else {
//                $this
//                    ->addField('start_time_time_text', TextType::class, [
//                        'label' => 'start_time',
//                        'required' => true,
//                        'editable' => false,
//                    ], null, $sectionName)
//                    ->addField('start_time_time', HiddenType::class, [
//                        'value' => $values['start_time_time'],
//                    ], null, $sectionName);
//            }
//
//            if ($allowChangeDate) {
//                $this->addField('end_time_date', DateType::class, [
//                    'label' => 'end_date',
//                    'placeholder' => 'select_end_date',
//                    'required' => true
//                ], [new RequiredValidator()], $sectionName);
//            } else {
//                $this
//                    ->addField('end_time_date_text', TextType::class, [
//                        'label' => 'end_date',
//                        'required' => true,
//                        'editable' => false,
//                    ], null, $sectionName)
//                    ->addField('end_time_date', HiddenType::class, [
//                        'value' => $values['end_time_date'],
//                    ], null, $sectionName);
//            }
//
//            if ($allowChangeTime) {
//                $this->addField('end_time_time', TimeType::class, [
//                    'label' => 'end_time',
//                    'placeholder' => 'select_end_time',
//                    'required' => true
//                ], [new RequiredValidator()], $sectionName);
//            } else {
//                $this
//                    ->addField('end_time_time_text', TextType::class, [
//                        'label' => 'end_time',
//                        'required' => true,
//                        'editable' => false,
//                    ], null, $sectionName)
//                    ->addField('end_time_time', HiddenType::class, [
//                        'value' => $values['end_time_time'],
//                    ], null, $sectionName);
//            }
//        } else {
//            $this
//                ->addField('start_time_date', DateType::class, [
//                    'label' => 'start_date',
//                    'required' => true,
//                    'placeholder' => 'select_start_date',
//                ], [new RequiredValidator()], $sectionName)
//                ->addField('start_time_time', TimeType::class, [
//                    'label' => 'start_time',
//                    'required' => true,
//                    'placeholder' => 'select_start_time',
//                ], [new RequiredValidator()], $sectionName)
//                ->addField('end_time_date', DateType::class, [
//                    'label' => 'end_date',
//                    'required' => true,
//                    'placeholder' => 'select_end_date',
//                ], [new RequiredValidator()], $sectionName)
//                ->addField('end_time_time', TimeType::class, [
//                    'label' => 'end_time',
//                    'required' => true,
//                    'placeholder' => 'select_end_time',
//                ], [new RequiredValidator()], $sectionName);
//        }

        if ($this->editing && $isRepeatEvent && !$allowChangeDate && !$allowChangeTime) {
            $this
                ->addField('start_time_text', TextType::class, [
                    'label' => 'start_date',
                    'editable' => false,
                ], null, $sectionName)
                ->addField('start_time', HiddenType::class, [
                    'value' => $values['start_time'],
                ], null, $sectionName)
                ->addField('end_time_text', TextType::class, [
                    'label' => 'end_time',
                    'editable' => false,
                ], null, $sectionName)
                ->addField('end_time', HiddenType::class, [
                    'value' => $values['end_time'],
                ], null, $sectionName);
        } else {
            $this
                ->addField('start_time', DateTimeType::class, [
                    'label' => 'start_time',
                    'required' => true,
                    'placeholder' => 'select_time',
                ], [new RequiredValidator()], $sectionName)
                ->addField('end_time', DateTimeType::class, [
                    'label' => 'end_time',
                    'required' => true,
                    'placeholder' => 'select_time',
                ], [new RequiredValidator()], $sectionName);
        }

        if ($this->editing && $isRepeatEvent) {
            $this
                ->addField('isrepeat', HiddenType::class, [
                    'value' => $values['isrepeat'],
                ])
                ->addField('after_number_event', HiddenType::class, [
                    'value' => $values['after_number_event'],
                ])
                ->addField('timerepeat', HiddenType::class, [
                    'value' => $values['timerepeat'],
                ]);
        } else {
            $sectionName = 'repeat';
            $this
                ->addSection($sectionName, ' ')
                ->addField('isrepeat', ChoiceType::class,
                    $this->getRepeatOptions(), [new RequiredValidator()], $sectionName)
                ->addField('after_number_event', IntegerType::class, [
                    'label' => 'end_repeat_after',
                    'placeholder' => 'enter_number_of_recurring_events',
                    'description' => $this->local->translate('maximum_number_events', [
                        'number' => Phpfox::getParam('fevent.fevent_max_instance_repeat_event')
                    ]),
                ], [new NumberRangeValidator(0, Phpfox::getParam('fevent.fevent_max_instance_repeat_event'))], $sectionName)
                ->addField('timerepeat', DateType::class, [
                    'label' => 'end_repeat_at',
                    'placeholder' => 'select_end_repeat_time',
                    'description' => 'please_choose_one_of_two_repeated_options'
                ], null, $sectionName);
        }

        $iMaxFileSizeKB = $this->setting->getUserSetting('fevent.max_upload_size_event');
        $iMaxFileSizeMB = $iMaxFileSizeKB > 0 ? $iMaxFileSizeKB / 1024 : 0;
        $iMaxFileSizeMB = number_format((float)$iMaxFileSizeMB, 1, '.', '');

        $sectionName = 'location';
        $this
            ->addSection($sectionName, ' ')
            ->addField('location', TextType::class, [
                'label' => 'location_venue',
                'placeholder' => 'enter_location',
                'required' => true
            ], [new RequiredValidator()], $sectionName)
            ->addField('address', TextType::class, [
                'label' => 'address',
                'placeholder' => 'enter_address'
            ], null, $sectionName)
            ->addField('city', TextType::class, [
                'label' => 'city',
                'inline' => true,
                'placeholder' => 'city_name'
            ], null, $sectionName)
            ->addField('postal_code', TextType::class, [
                'label' => 'postal_code',
                'inline' => true,
                'placeholder' => '- - - - - -'
            ], null, $sectionName)
            ->addCountryField(false, 'country', $sectionName);
        if (!$this->editing) {
            $this
                ->addField('file', FileType::class, [
                    'label' => 'featured_photo',
                    'file_type' => 'photo',
                    'item_type' => 'fevent',
                    'required' => true,
                    'preview_url' => $this->getPreviewImage(),
                    'max_upload_filesize' => $this->getSizeLimit($iMaxFileSizeKB),
                    'description' => $this->local->translate(
                        'you_can_upload_more_photos_by_edit_event_max_file_size_filesizemb_filesizekb',
                        [
                            'filesizemb' => $iMaxFileSizeMB,
                            'filesizekb' => $iMaxFileSizeKB,
                        ]),
                ], null, $sectionName);
        }

        $sectionName = 'settings';
        $this
            ->addSection($sectionName, 'settings')
            ->addField('notification_type', ChoiceType::class,
                $this->getNotificationReminderOptions(), [new RequiredValidator()], $sectionName)
            ->addField('notification_value', IntegerType::class, [
                'placeholder' => 'enter_number_of_minutes_hours_days',
                'description' => 'send_reminder_for_attending_maybe_attending_users',
            ], null, $sectionName);

        if (empty($this->data['item_id'])) {
            $this->addPrivacyField([
                'description' => 'control_who_can_see_this_event',
                'disable_custom' => true
            ], $sectionName, $this->privacy->getValue('fevent.display_on_profile'));
        }
        $this
            ->addModuleFields([
                'module_value' => 'fevent'
            ])
            ->addField('submit', SubmitType::class, [
                'label' => 'save'
            ]);
    }

    public function getTicketTypeOptions()
    {
        $ticketTypes = [
            [
                'label' => $this->local->translate('no_ticket'),
                'value' => 'no_ticket',
            ],
            [
                'label' => $this->local->translate('free'),
                'value' => 'free',
            ],
            [
                'label' => $this->local->translate('paid'),
                'value' => 'paid',
            ],
        ];

        return [
            'label' => 'ticket',
            'required' => true,
            'value_default' => 'no_ticket',
            'options' => $ticketTypes
        ];
    }

    public function getRepeatOptions()
    {
        return [
            'label' => 'repeat',
            'required' => true,
            'value_default' => -1,
            'options' => [
                [
                    'label' => $this->local->translate('no_repeat'),
                    'value' => -1
                ],
                [
                    'label' => $this->local->translate('daily'),
                    'value' => 0
                ],
                [
                    'label' => $this->local->translate('weekly'),
                    'value' => 1
                ],
                [
                    'label' => $this->local->translate('monthly'),
                    'value' => 2
                ],
            ],
        ];
    }

    public function getNotificationReminderOptions()
    {
        return [
            'label' => 'notification_reminder',
            'required' => true,
            'value_default' => 'no_remind',
            'options' => [
                [
                    'label' => $this->local->translate('no_remind'),
                    'value' => 'no_remind'
                ],
                [
                    'label' => $this->local->translate('minutes_uc'),
                    'value' => 'minute'
                ],
                [
                    'label' => $this->local->translate('hours_uc'),
                    'value' => 'hour'
                ],
                [
                    'label' => $this->local->translate('days_uc'),
                    'value' => 'day'
                ],
            ],
        ];
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

    public function getPreviewImage()
    {
        if (isset($this->data['image'])) {
            if (isset($this->data['image']['200'])) {
                return $this->data['image']['200'];
            } elseif (isset($this->data['image']['image_url'])) {
                return $this->data['image']['image_url'];
            }
        }
        return null;
    }

    public function getAttachments()
    {
        return (isset($this->data['attachments']) ? $this->data['attachments'] : null);
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
        $values = $this->getValues();

        foreach ($this->fields as $field) {
            if (!$field->isValid()) {
                $bValid = false;
                $this->invalidFields[$field->getName()] = $field->getErrorMessage();
            } else {
                // custom validating
                $fieldName = $field->getName();
                $value = $field->getValue();
                switch ($fieldName) {
                    case 'ticket_price':
                        if ($values['ticket_type'] == 'paid') {
                            $ticketPriceLength = mb_strlen($value);
                            if ($ticketPriceLength < 1 || $ticketPriceLength > 50) {
                                $bValid = false;
                                $this->invalidFields[$fieldName] = $field->getErrorMessage();
                            }
                        }
                        break;
                    case 'timerepeat':
                        if (($values['isrepeat'] != '-1') && empty($values['after_number_event'])) {
                            if (empty($value)) {
                                $bValid = false;
                                $this->invalidFields[$fieldName] = $field->getErrorMessage();
                            }
                        }
                        break;
                    case 'notification_value':
                        if ($values['notification_type'] != 'no_remind') {
                            if (!is_numeric($value) || $value <= 0) {
                                $bValid = false;
                                $this->invalidFields[$fieldName] = $this->local->translate('field_name_field_is_invalid', [
                                    'field_name' => $this->local->translate('notification_value'),
                                ]);;
                            }
                        }
                        break;
                }
            }
        }
        return $bValid;
    }
}