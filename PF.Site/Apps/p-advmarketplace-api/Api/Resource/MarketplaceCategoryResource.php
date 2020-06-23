<?php


namespace Apps\P_AdvMarketplaceAPI\Api\Resource;

use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Phpfox;

class MarketplaceCategoryResource extends ResourceBase
{
    const RESOURCE_NAME = "advancedmarketplace-category";
    public $resource_name = self::RESOURCE_NAME;

    /**
     * Custom ID Field Name
     */
    protected $idFieldName = "category_id";

    public $name;
    public $name_url;
    public $used;
    public $ordering;
    public $is_active;
    public $subs;
    public $parent_id;

    /**
     * Get detail url
     * @return string
     */
    public function getLink()
    {
        return Phpfox::permalink('marketplace.category', $this->id, $this->name);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->parse->cleanOutput($this->getLocalization()->translate($this->name));
    }


    public function getSubs()
    {
        if (!empty($this->rawData['sub'])) {
            $subs = [];
            foreach ($this->rawData['sub'] as $sub) {
                $subs[] = MarketplaceCategoryResource::populate($sub)->displayShortFields()->toArray();
            }
            return $subs;
        }
        return null;
    }

    public function getShortFields()
    {
        return ['id', 'name', 'subs', 'resource_name'];
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('used', ['type' => ResourceMetadata::INTEGER])
            ->mapField('ordering', ['type' => ResourceMetadata::INTEGER])
            ->mapField('is_active', ['type' => ResourceMetadata::BOOL])
            ->mapField('parent_id', ['type' => ResourceMetadata::INTEGER]);
    }

    public function getMobileSettings($params = [])
    {
        return self::createSettingForResource([
            'schema'=>[
                'ref'=>'category'
            ],
            'resource_name' => $this->getResourceName(),
            'fab_buttons'   => false,
        ]);
    }
}