<?php
namespace Apps\P_AdvMarketplaceAPI\Api\Resource;

use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Phpfox;

class MarketplacePhotoResource extends ResourceBase
{
    const RESOURCE_NAME = "advancedmarketplace-photo";
    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = 'image_id';

    public $image;
    public $main;

    public function getImage()
    {
        $sizes = Phpfox::getParam('advancedmarketplace.thumbnail_sizes');

        if (!empty($this->rawData['image_path'])) {
            return Image::createFrom([
                'file' => $this->rawData['image_path'],
                'server_id' => $this->rawData['server_id'],
                'path' => 'advancedmarketplace.url_pic'
            ],$sizes);
        } else {
            return $this->getDefaultImage();
        }
    }

}