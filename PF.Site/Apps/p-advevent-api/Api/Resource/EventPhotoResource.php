<?php


namespace Apps\P_AdvEventAPI\Api\Resource;


use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Phpfox;

class EventPhotoResource extends ResourceBase
{
    const RESOURCE_NAME = "fevent-photo";
    public $resource_name = self::RESOURCE_NAME;

    /**
     * Custom ID Field Name
     */
    protected $idFieldName = "image_id";

    public $event_id;
    public $image;

    public $main;

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        if (!empty($this->rawData['image_path'])) {
            $aSizes = Phpfox::getParam('fevent.thumbnail_sizes');
            $image = Image::createFrom([
                'file' => $this->rawData['image_path'],
                'server_id' => $this->rawData['server_id'],
                'path' => 'event.url_image'
            ], ['square']); // ???
            return $image;
        } else {
            return $this->getDefaultImage();
        }
    }
}