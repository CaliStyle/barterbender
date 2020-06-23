<?php


namespace Apps\P_AdvEventAPI\Service;


use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Service\NameResource;

use Apps\P_AdvEventAPI\Api\Resource\EventAdminResource;
use Apps\P_AdvEventAPI\Api\Resource\EventResource;
use Apps\P_AdvEventAPI\Api\Security\EventAccessControl;

use Apps\Core_MobileApi\Service\Helper\Pagination;
use Phpfox;


class EventAdminApi extends AbstractResourceApi
{
    private $eventService;
    /**
     * @var Process
     */
    private $processService;

    /**
     * PageAdminApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->eventService = Phpfox::getService('fevent.process');
        $this->processService = Phpfox::getService('fevent.process');
    }

    /**
     * @param array $params
     * @return mixed
     */

    function findAll($params = [])
    {
        $params = $this->resolver
            ->setDefined([
                'id', 'limit', 'page', 'q', 'is_manage'
            ])
            ->setAllowedTypes('limit', 'int', [
                'min' => Pagination::DEFAULT_MIN_ITEM_PER_PAGE,
                'max' => Pagination::DEFAULT_MAX_ITEM_PER_PAGE
            ])
            ->setAllowedTypes('page', 'int')
            ->setAllowedTypes('id', 'int')
            ->setRequired(['id'])
            ->setDefault([
                'page' => 1,
                'limit' => Pagination::DEFAULT_ITEM_PER_PAGE
            ])
            ->resolve($params)->getParameters();

        if (!$this->resolver->isValid()) {
            $this->validationParamsError($this->resolver->getInvalidParameters());
        }
        if (!$this->getSetting()->getUserSetting('fevent.can_access_event')) {
            return $this->permissionError();
        }

        $event = NameResource::instance()->getApiServiceByResourceName(EventResource::RESOURCE_NAME)->loadResourceById($params['id']);
        if (!$event) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(EventAccessControl::EDIT, EventResource::populate($event));

        if ($event['view_id'] != '0' && !($this->getSetting()->getUserSetting('fevent.can_approve_events') || Phpfox::getUserParam('fevent.can_edit_other_event') ||
                Phpfox::getUserParam('fevent.can_delete_other_event'))
        ) {
            return $this->permissionError();
        }

        $eventId = (int)$params['id'];
        $admins = $this->database()->select(Phpfox::getUserField() . ', ea.event_id')
            ->from(Phpfox::getT('fevent_admin'), 'ea')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ea.user_id')
            ->where('ea.event_id = ' . $eventId)
            ->execute('getSlaveRows');
        if (!empty($admins) && empty($params['is_manage'])) {
            foreach ($admins as $key => $admin) {
                $admins[$key]['event_id'] = $eventId;
            }
        }
        $this->processRows($admins);
        return $this->success($admins);
    }

    /**
     * @param $params
     * @return mixed
     */
    function findOne($params)
    {
        return null;
    }

    /**
     * @param $params
     * @return mixed
     */
    function create($params)
    {
        $params = $this->resolver->setDefined(['user_ids', 'id'])
            ->setRequired(['id'])
            ->resolve($params)
            ->getParameters();

        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getMissing());
        }

        $event = NameResource::instance()
            ->getApiServiceByResourceName(EventResource::RESOURCE_NAME)
            ->loadResourceById($params['id'], true);

        if (empty($event)) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(EventAccessControl::EDIT, $event);

        $id = $this->processCreate($params, $event);
        return $this->success([
            'id' => $id
        ], [], $this->getLocalization()->translate('event_successfully_updated'));
    }

    private function processCreate($values, EventResource $event)
    {
        $iId = $values['id'];
        $aOldAdmins = $this->database()->select('user_id')->from(':fevent_admin')->where(['event_id' => (int)$iId])->executeRows();
        $aOldAdminIds = array_column($aOldAdmins, 'user_id');
        $aAdmins = !is_array($values['user_ids']) ? explode(',', $values['user_ids']) : $values['user_ids'];
        if (count($aAdmins)) {
            $aUserCache = $aOldAdminIds;
            foreach ($aAdmins as $iAdmin) {
                if (in_array($iAdmin, $aUserCache)) {
                    continue;
                }
                if (!Phpfox::getService('user')->isUser($iAdmin, true)) {
                    continue;
                }
                if ($event->getAuthor()->getId() == $iAdmin) {
                    continue;
                }

                $aUserCache[] = $iAdmin;

                if (Phpfox::isModule('notification')) {
                    Phpfox::getService('notification.process')->add('fevent_admins', $iId, $iAdmin);
                }

                $this->database()->insert(Phpfox::getT('fevent_admin'), array('event_id' => $iId, 'user_id' => $iAdmin));
            }
        }

        return $iId;
    }

    /**
     * @param $params
     * @return mixed
     */
    function update($params)
    {
        $params = $this->resolver
            ->setRequired(['id', 'user_ids'])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getMissing());
        }
        return $this->create($params);
    }

    /**
     * @param $params
     * @return mixed
     */
    function patchUpdate($params)
    {
        // TODO: Implement updateAll() method.
    }

    /**
     * @param $params
     * @return mixed
     */
    function delete($params)
    {
        $params = $this->resolver
            ->setRequired(['id', 'user_id'])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->missingParamsError($this->resolver->getMissing());
        }
        $event = NameResource::instance()->getApiServiceByResourceName(EventResource::RESOURCE_NAME)->loadResourceById($params['id'], true);
        $admin = $this->database()
            ->select('*')
            ->from(':fevent_admin')
            ->where('event_id = ' . (int)$params['id'] . ' AND user_id = ' . (int)$params['user_id'])
            ->execute('getSlaveRow');
        if (!$event || !$admin) {
            return $this->notFoundError();
        }

        if ($this->getAccessControl()->isGranted(EventAccessControl::EDIT, $event)) {
            $this->database()->delete(':fevent_admin', 'user_id = ' . (int)$params['user_id'] . ' AND event_id = ' . (int)$params['id']);
            return $this->success([], [], $this->getLocalization()->translate('event_successfully_updated'));
        }
        return $this->permissionError();
    }

    /**
     * @param array $params
     * @return mixed
     */
    function form($params = [])
    {
        // TODO: Implement form() method.
    }

    /**
     * @param $id
     * @return mixed
     */
    function loadResourceById($id, $returnResource = false)
    {
        // TODO: Implement loadResourceById() method.
    }

    public function processRow($item)
    {
//        return UserResource::populate($item)->displayShortFields()->toArray();
        return EventAdminResource::populate($item)->displayShortFields()->toArray();
    }

    /**
     * Create custom access control layer
     */
    public function createAccessControl()
    {
        $this->accessControl =
            new EventAccessControl($this->getSetting(), $this->getUser());
    }

    public function searchFriendFilter($id, $friends)
    {
        $aAdmins = $aEvent['admins'] = $this->database()->select(Phpfox::getUserField())
            ->from(Phpfox::getT('fevent_admin'), 'ca')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ca.user_id')
            ->where('ca.event_id = ' . $id)
            ->execute('getSlaveRows');
        $aAdminId = [];
        if (!empty($aAdmins)) {
            $aAdminId = array_map(function ($value) {
                return $value['user_id'];
            }, $aAdmins);
        }

        if (!empty($aAdminId)) {
            foreach ($friends as $iKey => $friend) {
                if (in_array($friend['user_id'], $aAdminId)) {
                    $friends[$iKey]['is_active'] = $this->getLocalization()->translate('is_admin');
                }
            }
        }
        return $friends;
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
}