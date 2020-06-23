<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Service;

use Apps\Core_MobileApi\Adapter\MobileApp\MobileApp;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileAppSettingInterface;
use Apps\Core_MobileApi\Adapter\MobileApp\ScreenSetting;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Api\Resource\AnnouncementResource;
use Apps\Core_MobileApi\Api\Security\Announcement\AnnouncementAccessControl;
use Phpfox;

class AnnouncementApi extends AbstractResourceApi implements MobileAppSettingInterface
{

    protected $announceService;

    protected $processService;

    public function __construct()
    {
        parent::__construct();
        $this->announceService = Phpfox::getService('announcement');
        $this->processService = Phpfox::getService('announcement.process');
    }

    function findAll($params = [])
    {
        $this->denyAccessUnlessGranted(AnnouncementAccessControl::VIEW);

        $announcements = $this->announceService->getLatest(null, true, Phpfox::getTime());

        //Reset array key
        $announcements = array_values($announcements);
        $this->processRows($announcements);
        return $this->success($announcements);
    }

    function findOne($params)
    {
        $this->denyAccessUnlessGranted(AnnouncementAccessControl::VIEW);
        $id = $this->resolver->setRequired(['id'])->resolveId($params);

        if (!$id) {
            return $this->missingParamsError($this->resolver->getInvalidParameters());
        }
        $announcement = $this->announceService->getLatest($id);

        if (empty($announcement) || !count($announcement)) {
            return $this->notFoundError();
        }
        if (is_array($announcement)) {
            $announcement = reset($announcement);
        }
        $resource = AnnouncementResource::populate($announcement);

        return $this->success($resource->setExtra($this->getAccessControl()->getPermissions($resource))->toArray());
    }

    function create($params)
    {
        // TODO: Implement create() method.
    }

    function update($params)
    {
        // TODO: Implement update() method.
    }

    function patchUpdate($params)
    {
        // TODO: Implement patchUpdate() method.
    }

    function delete($params)
    {
        $id = $this->resolver->setRequired(['id'])->resolveId($params);

        if (!$id) {
            return $this->missingParamsError($this->resolver->getInvalidParameters());
        }
        $item = $this->loadResourceById($id, true);
        if (empty($item)) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(AnnouncementAccessControl::CLOSE, $item);
        if ($this->processService->hide($id)) {
            return $this->success();
        }
        return $this->error();
    }

    function form($params = [])
    {
        // TODO: Implement form() method.
    }

    function approve($params)
    {
        // TODO: Implement approve() method.
    }

    function feature($params)
    {
        // TODO: Implement feature() method.
    }

    function sponsor($params)
    {
        // TODO: Implement sponsor() method.
    }

    function loadResourceById($id, $returnResource = false)
    {
        $item = $this->database()->select('*')
            ->from(':announcement')
            ->where(['announcement_id' => (int)$id])
            ->executeRow();
        if (empty($item)) {
            return null;
        }
        $item['subject_var'] = $this->getLocalization()->translate('subject_var');
        $item['intro_var'] = $this->getLocalization()->translate('intro_var');
        $item['content_var'] = $this->getLocalization()->translate('content_var');
        if ($returnResource) {
            return AnnouncementResource::populate($item);
        }
        return $item;
    }

    public function processRow($item)
    {
        $resource = AnnouncementResource::populate($item);
        return $resource
            ->setExtra($this->getAccessControl()->getPermissions($resource))
            ->displayShortFields()
            ->toArray();
    }

    public function createAccessControl()
    {
        $this->accessControl = new AnnouncementAccessControl($this->getSetting(), $this->getUser());
    }

    public function getAppSetting($param)
    {
        $l = $this->getLocalization();
        $app = new MobileApp('announcement', [
            'title'         => $l->translate('announcements'),
            'home_view'     => 'menu',
            'main_resource' => new AnnouncementResource([]),
        ], $param['api_version_name']);

        return $app;
    }

    public function getScreenSetting($param)
    {
        $l = $this->getLocalization();
        $screenSetting = new ScreenSetting('announcement', []);
        $resourceName = AnnouncementResource::populate([])->getResourceName();
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_DETAIL, [
            ScreenSetting::LOCATION_HEADER => [
                'component'   => 'simple_header',
                'headerTitle' => $l->translate('announcement')
            ],
            ScreenSetting::LOCATION_MAIN   => [
                'component'       => 'item_simple_detail',
                'embedComponents' => [
                    'item_title',
                    'item_author',
                    'item_description',
                    'item_html_content',
                ]
            ],
            'screen_title'                 => $l->translate('announcements') . ' > ' . $l->translate('announcement') . ' - ' . $l->translate('mobile_detail_page')
        ]);

        return $screenSetting;
    }
}