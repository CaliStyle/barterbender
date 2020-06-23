<?php

namespace Apps\P_AdvEventAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\MultiFileType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\ChoiceType;

class EventPhotoForm extends GeneralForm
{
    /**
     * Photo Limitation
     */
    protected $maxFiles;

    protected $action = "fevent-photo";

    protected $recurring = false;

    public function setRecurring($recurring)
    {
        $this->recurring = $recurring ? true : false;
    }

    public function buildForm($option = null, $data = [])
    {
        $sectionName = 'basic';
        $this->addSection($sectionName, 'basic_info');

        if ($this->recurring) {
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
                ]);
        }

        $this->addField('files', MultiFileType::class, [
            'label' => 'photos',
            'min_files' => 0,
            'max_files' => $this->maxFiles,
            'file_type' => 'photo',
            'item_type' => 'fevent',
            'current_files' => $this->getCurrentImages(),
            'value' => $this->getCurrentValue(),
            'max_upload_filesize' => $this->getSizeLimit($this->setting->getUserSetting('fevent.max_upload_size_event'))
        ])
            ->addField('submit', SubmitType::class, [
                'label' => 'save'
            ]);
    }

    private function getCurrentImages()
    {
        if (!empty($this->data)) {
            $images = [];
            foreach ($this->data as $image) {
                if (!empty($image['image'])) {
                    $images[] = [
                        'id' => $image['id'],
                        'url' => isset($image['image']['200']) ? $image['image']['200'] : $image['image']['image_url'],
                        'default' => !!$image['main']
                    ];
                }
            }
            return $images;
        }
        return null;
    }

    private function getCurrentValue()
    {
        if (!empty($this->data) && is_array($this->data)) {
            $images = [];
            foreach ($this->data as $image) {
                if (isset($image['id'])) {
                    $images['order'][] = $image['id'];
                    if (!empty($image['main'])) {
                        $images['default'] = $image['id'];
                    }
                }
            }
            return $images;
        }
        return null;
    }

    public function getMaxFiles()
    {
        return $this->maxFiles;
    }

    public function setMaxFiles($number)
    {
        $this->maxFiles = $number;
    }
}