<?php
/**
 * Created by PhpStorm.
 * User: pro
 * Date: 20/4/18
 * Time: 5:29 PM
 */

namespace Apps\Core_MobileApi\Api\Resource;


use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;

class AttachmentResource extends ResourceBase
{
    const RESOURCE_NAME = "attachment";
    public $resource_name = self::RESOURCE_NAME;

    public $item_type;
    public $item_id;
    public $file_name;
    public $file_size;
    public $extension;
    public $is_video;
    public $is_image;
    public $download_url;
    public $description;

    /**
     * @var UserResource
     */
    public $user;


    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getItemType()
    {
        return $this->rawData['category_id'];
    }

    /**
     * @return mixed
     */
    public function getDownloadUrl()
    {
        $this->download_url = $this->is_image ? \Phpfox::getLib('image.helper')->display([
            'server_id'  => $this->rawData['server_id'],
            'title'      => $this->rawData['description'],
            'path'       => 'core.url_attachment',
            'file'       => $this->rawData['destination'],
            'suffix'     => '_view',
            'max_width'  => 'attachment.attachment_max_medium',
            'max_height' => 'attachment.attachment_max_medium',
            'return_url' => true
        ]) : '';
        return $this->download_url;
    }

    public function getShortFields()
    {
        return [
            'id', 'file_name', 'download_url', 'is_image', 'is_video'
        ];
    }

    public function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('item_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('file_size', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_video', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_image', ['type' => ResourceMetadata::BOOL]);
    }


}