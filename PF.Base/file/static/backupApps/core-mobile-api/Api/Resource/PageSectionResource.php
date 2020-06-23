<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Api\Resource;

use Apps\Core_MobileApi\Adapter\MobileApp\Screen;


class PageSectionResource extends ResourceBase
{
    public $resource_name = 'pages_section';

    public function getMobileSettings($params = [])
    {
        $l = $this->getLocalization();

        return self::createSettingForResource([
            'resource_name' => $this->getResourceName(),
            'urls.base'     => 'mobile/page-home',
            'schema'        => [
                'definition' => [
                    'main'  => 'page_type',
                    'items' => 'pages[]'
                ]
            ],
            'search_input'  => [
                'placeholder'   => $l->translate('search_pages'),
                'resource_name' => 'pages'
            ],
            'list_view'     => [
                'apiUrl'     => 'mobile/page-home',
                'item_view'  => 'section_item',
                'item_props' => ['child_item_view' => 'pages_item_card_view'],
                'layout'     => Screen::LAYOUT_LIST_CARD_VIEW,
            ],
            'fab_buttons'   => false,
            'can_add'       => false,
            'app_menu'      => [
                ['label' => $l->translate('pages'), 'params' => ['initialQuery' => ['view' => '']]],
            ],
        ]);
    }
}