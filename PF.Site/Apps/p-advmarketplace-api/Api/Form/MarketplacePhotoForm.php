<?php
namespace Apps\P_AdvMarketplaceAPI\Api\Form;

use Apps\Core_MobileApi\Api\Form\GeneralForm;
use Apps\Core_MobileApi\Api\Form\Type\MultiFileType;
use Apps\Core_MobileApi\Api\Form\Type\SubmitType;

class MarketplacePhotoForm extends GeneralForm
{
    /**
     * Photo Limitation
     */
    protected $maxFiles;
    protected $action = "advancedmarketplace-photo";

    public function buildForm($option = null, $data = [])
    {
        $this->addField('files',MultiFileType::class ,[
            'label' => 'photos',
            'min_files' => 0,
            'max_files' => $this->maxFiles,
            'file_type' => 'photo',
            'item_type' => 'advancedmarketplace',
            'current_files' => $this->getCurrentImages(),
            'value' => $this->getCurrentValue(),
            'max_upload_filesize' => $this->getSizeLimit($this->setting->getUserSetting('advancedmarketplace.max_upload_size_listing'))
        ])
        ->addField('submit', SubmitType::class, [
            'label' => 'save'
        ]);
    }

    private function getCurrentImages()
    {
        if(!empty($this->data)){
            $images = [];
            foreach($this->data as $image) {
                if(!empty($image['image'])){
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