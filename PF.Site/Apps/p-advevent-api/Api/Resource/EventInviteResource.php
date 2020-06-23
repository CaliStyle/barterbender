<?php


namespace Apps\P_AdvEventAPI\Api\Resource;
use Apps\Core_MobileApi\Api\Resource\ResourceBase;

use Apps\Core_MobileApi\Api\Mapping\ResourceMetadata;
use Phpfox;

class EventInviteResource extends ResourceBase
{
    const RESOURCE_NAME = "fevent-invite";
    public $resource_name = self::RESOURCE_NAME;

    protected $idFieldName = 'invite_id';

    public $event_id;
    public $type_id;
    public $rsvp_id;

    public $invited_email;

    /**
     * @var UserResource
     */
    public $user;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return Phpfox::permalink('fevent', $this->event_id);
    }

    protected function loadMetadataSchema(ResourceMetadata $metadata = null)
    {
        parent::loadMetadataSchema($metadata);
        $this->metadata
            ->mapField('event_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('type_id', ['type' => ResourceMetadata::INTEGER])
            ->mapField('rsvp_id', ['type' => ResourceMetadata::INTEGER]);
    }
}