<?php


namespace Apps\P_AdvMarketplaceAPI\Api\Resource;

use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;
use Phpfox;

class MarketplaceInviteResource extends ResourceBase
{
    const RESOURCE_NAME = "advancedmarketplace-invite";
    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = "invite_id";

    public $marketplace_id;
    public $type_id;
    public $visited_id;

    public $invited_email;

    public $user;

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
        return Phpfox::permalink('advancedmarketplace', $this->marketplace_id);
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('marketplace_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('type_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('visited_id', ['type' => ResourceMetadata::INTEGER]);
    }

    public function getMobileSettings($params = [])
    {
        return self::createSettingForResource([
            'schema'=>[
                'ref'=>'item_invite',
            ],
            'resource_name' => $this->getResourceName(),
            'urls.base'     => 'mobile/advancedmarketplace-invite',
            'search_input'  => false,
            'list_view'     => [
                'item_view' => 'page_member',
            ],
            'fab_buttons'   => false,
            'can_add'       => false,
        ]);
    }
}