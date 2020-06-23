<?php


namespace Apps\Core_MobileApi\Api\Form\Photo;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\CheckboxType;
use Apps\Core_MobileApi\Api\Form\Type\ChoiceType;
use Apps\Core_MobileApi\Api\Form\Type\HiddenType;
use Apps\Core_MobileApi\Api\Form\Type\HierarchyType;
use Apps\Core_MobileApi\Api\Form\Type\MultiFileType;
use Apps\Core_MobileApi\Api\Form\Type\RadioType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;
use Apps\Core_MobileApi\Api\Form\Type\TagsType;
use Apps\Core_MobileApi\Api\Form\Type\TextareaType;
use Apps\Core_MobileApi\Api\Form\Type\TextType;
use Apps\Core_MobileApi\Api\Form\Validator\NumberRangeValidator;
use Apps\Core_MobileApi\Api\Form\Validator\StringLengthValidator;
use Apps\Core_MobileApi\Api\Form\Validator\TypeValidator;

class PhotoForm extends GeneralForm
{

    protected $categories;
    protected $albums = [];
    protected $tags;
    protected $editing = false;
    protected $action = "photo";
    protected $albumId = 0;
    protected $canMature = true;

    /**
     * @param null  $options
     * @param array $data
     *
     * @return mixed|void
     * @throws \Apps\Core_MobileApi\Api\Exception\ErrorException
     */
    function buildForm($options = null, $data = [])
    {

        if ($this->setting->getAppSetting('photo.allow_photo_category_selection')) {
            $this->addField('categories', HierarchyType::class, [
                'rawData'  => $this->categories,
                'order'    => 3,
                'multiple' => true,
                'label'    => 'categories',
            ], [new TypeValidator(TypeValidator::IS_ARRAY_NUMERIC)]);
        }
        if (!empty($this->albums)) {
            $this->addField('album', ChoiceType::class, [
                'options' => array_map(function ($item) {
                    return [
                        'value' => (int)$item['album_id'],
                        'label' => $item['name']
                    ];
                }, $this->albums),
                'order'   => 2,
                'label'   => 'album',
                'value'   => !empty($this->albumId) ? (int)$this->albumId : null
            ]);
        }
        if ($this->getEditing()) {
            $this
                ->addField('title', TextType::class, [
                    'label'       => 'Title',
                    'placeholder' => 'title',
                    'order'       => 4
                ], [new StringLengthValidator(1, 250)])
                ->addField('text', TextareaType::class, [
                    'label' => 'description',
                    'order' => 5
                ])
                ->addField('tags', TagsType::class, [
                    'label' => 'topics',
                    'order' => 6
                ]);
            if ($this->getCanMature()) {
                $this->addField('mature', RadioType::class, [
                    'label'         => 'mature_content',
                    'value_default' => 0,
                    'options'       => $this->_getMatureValue(),
                    'order'         => 7
                ], [new NumberRangeValidator(0, 2)]);
            }

            $this->addField('allow_download', CheckboxType::class, [
                'label'         => 'download_enabled',
                'value_default' => 1,
                'order'         => 8
            ]);
            if (empty($this->data['album']) && empty($this->data['group_id'])) {
                $this->addPrivacyField([], null, $this->privacy->getValue('photo.default_privacy_setting'));
            }
        } else {
            if (!$this->albumId && empty($this->data['item_id']) && empty($this->data['module_id'])) {
                $this->addPrivacyField([
                    'hidden_by'    => '!album',
                    'hidden_value' => [null, '0', 0],
                ], null, $this->privacy->getValue('photo.default_privacy_setting'));
            }
            $this->addField('files', MultiFileType::class, [
                'label'               => 'select_images',
                'min_files'           => 1,
                'max_files'           => $this->setting->getUserSetting('photo.max_images_per_upload'),
                'item_type'           => 'photo',
                'file_type'           => 'photo',
                'required'            => true,
                'allow_temp_default'  => false,
                'max_upload_filesize' => $this->getSizeLimit($this->setting->getUserSetting('photo.photo_max_upload_size')),
            ]);
        }
        $this
            //Set photo is album cover when edit
            ->addField('set_album_cover', HiddenType::class, [], [new NumberRangeValidator(0)])
            //Support upload photo in feed type_id = 1 -> photo add to album timeline
            ->addField('type_id', HiddenType::class, [], [new NumberRangeValidator(0)])
            //Support checkin in feed
            ->addField('location', HiddenType::class)
            ->addModuleFields()
            ->addField('submit', SubmitType::class, [
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
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param $albums
     */
    public function setAlbums($albums)
    {
        $this->albums = $albums;
    }

    /**
     * @param $edit
     *
     * @return $this
     */
    public function setEditing($edit)
    {
        $this->editing = $edit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEditing()
    {
        return $this->editing;
    }

    private function _getMatureValue()
    {
        $mature = [
            [
                'value' => 0,
                'label' => $this->local->translate('no')
            ],
            [
                'value' => 1,
                'label' => $this->local->translate('yes_warning')
            ],
            [
                'value' => 2,
                'label' => $this->local->translate('yes_strict')
            ],

        ];
        return $mature;
    }

    public function setCanMature($canMature)
    {
        $this->canMature = $canMature;
        return $this;
    }

    public function getCanMature()
    {
        return $this->canMature;
    }

    public function buildValues()
    {
        if (!$this->isPost) {
            //Is get edit form
            if (!empty($this->data['album'])) {
                $this->data['album'] = (int)$this->data['album'][0]['id'];
            } else if (!empty($this->albumId)) {
                $this->data['album'] = (int)$this->albumId;
            }
        }
        parent::buildValues();
    }

    public function setAlbumId($id)
    {
        $this->albumId = $id;
        return $this;
    }
}