<?php

namespace Apps\Core_MobileApi\Api\Resource;

use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\Object\Image;
use Apps\Core_MobileApi\Api\Resource\Object\Statistic;

class AdResource extends ResourceBase
{
    const RESOURCE_NAME = "ad";

    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = "ads_id";

    public $name;
    public $is_custom;
    public $is_active;
    public $is_cpm;

    /**
     * @var Statistic
     */
    public $statistic;

    public $image;
    public $title;
    public $body;
    public $trimmed_url;

    public $block_id;
    public $module_access;
    public $country_iso;
    public $genre;
    public $age_from;
    public $age_to;
    public $disallow_controller;
    public $country_child_id;
    public $countries_list;
    public $start_date;
    public $end_date;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return !isset($this->rawData['url_link']) ? $this->rawData['url_link'] : \Phpfox::getLib('url')->makeUrl('ad', ['id' => $this->getId()]);
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('title', ['type' => ResourceMetadata::STRING])
            ->mapField('is_custom', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_active', ['type' => ResourceMetadata::BOOL])
            ->mapField('is_cpm', ['type' => ResourceMetadata::BOOL])
            ->mapField('block_id', ['type' => ResourceMetadata::INTEGER]);
    }

    /**
     * @return Image|null
     */
    public function getImage()
    {
        if (!empty($this->rawData['image_path'])) {
            return Image::createFrom([
                'file'      => $this->rawData['image_path'],
                'server_id' => $this->rawData['server_id'],
                'path'      => 'ad.url_image'
            ]);
        }
        return null;

    }

    public function setStatistic($statistic)
    {
        $statistic->total_click = isset($this->rawData['total_click']) ? (int)$this->rawData['total_click'] : 0;
        $statistic->count_view = isset($this->rawData['count_view']) ? (int)$this->rawData['count_view'] : 0;
        $statistic->count_click = isset($this->rawData['count_click']) ? (int)$this->rawData['count_click'] : 0;
        $this->statistic = $statistic;
    }

    public function getShortFields()
    {
        return ['id', 'link', 'resource_name', 'title', 'body', 'image', 'statistic', 'is_active', 'block_id'];
    }
}