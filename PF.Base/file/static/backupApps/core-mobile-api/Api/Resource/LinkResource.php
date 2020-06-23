<?php

namespace Apps\Core_MobileApi\Api\Resource;

use Apps\Core_MobileApi\Api\Form\Validator\Filter\TextFilter;

class LinkResource extends ResourceBase
{

    const RESOURCE_NAME = "link";

    public $resource_name = self::RESOURCE_NAME;

    public $title;
    public $description;
    public $image;
    public $link;
    public $embed_code;
    public $user;
    public $host;

    public function getShortFields()
    {
        return ['resource_name', 'id', 'title', 'description', 'image', 'link', 'host'];
    }

    public function getImage()
    {
        if ($this->image && strpos($this->image, 'http') === false) {
            $this->image = (PHPFOX_IS_HTTPS ? 'https:' : 'http:') . $this->image;
        }
        return $this->image ? htmlspecialchars_decode($this->image) : $this->image;
    }

    public function getDescription()
    {
        if ($this->description) {
            return TextFilter::pureText($this->description, 255, true);
        }
        return null;
    }

    public function getHost()
    {
        $parts = parse_url($this->link);
        $this->host = isset($parts['host']) ? $parts['host'] : '';
        return $this->host;
    }
}