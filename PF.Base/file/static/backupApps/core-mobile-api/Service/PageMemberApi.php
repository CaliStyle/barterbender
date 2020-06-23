<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Service;

use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Api\Resource\PageMemberResource;
use Apps\Core_MobileApi\Api\Resource\PageResource;
use Apps\Core_MobileApi\Api\Security\Group\GroupAccessControl;
use Apps\Core_MobileApi\Service\Helper\Pagination;
use Apps\PHPfox_Groups\Service\Facade;
use Apps\PHPfox_Groups\Service\Groups;
use Apps\PHPfox_Groups\Service\Process;
use Phpfox;


class PageMemberApi extends AbstractResourceApi
{

    /**
     * @var Facade
     */
    private $facadeService;

    /**
     * @var Groups
     */
    private $pageService;
    /**
     * @var Process
     */
    private $processService;

    /**
     * GroupAdminApi constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->facadeService = Phpfox::getService('pages.facade');
        $this->pageService = Phpfox::getService('pages');
        $this->processService = Phpfox::getService('pages.process');
    }

    /**
     * @param array $params
     *
     * @return mixed
     */

    function findAll($params = [])
    {
        $params = $this->resolver->setDefined([
            'page_id', 'limit', 'page', 'q',
        ])
            ->setAllowedTypes('limit', 'int', [
                'min' => Pagination::DEFAULT_MIN_ITEM_PER_PAGE,
                'max' => Pagination::DEFAULT_MAX_ITEM_PER_PAGE
            ])
            ->setAllowedTypes('page', 'int')
            ->setAllowedTypes('page_id', 'int')
            ->setRequired(['page_id'])
            ->setAllowedValues('view', ['all', 'pending'])
            ->setDefault([
                'limit' => Pagination::DEFAULT_ITEM_PER_PAGE,
                'page'  => 1,
            ])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            $this->validationParamsError($this->resolver->getInvalidParameters());
        }
        if (!Phpfox::getUserParam('pages.can_view_browse_pages')) {
            return $this->permissionError();
        }
        $page = $this->pageService->getForView($params['page_id']);
        if (!$page) {
            return $this->notFoundError();
        }
        $canModerate = (Phpfox::getUserParam('pages.can_approve_pages') || Phpfox::getUserParam('pages.can_edit_all_pages') ||
            Phpfox::getUserParam('pages.can_delete_all_pages') || $page['is_admin']);
        if ($page['view_id'] == '2' || ($page['view_id'] != '0' && !$canModerate && ($this->getUser()->getId() != $page['user_id']))) {
            return $this->notFoundError();
        }
        if ($page['view_id'] != '0' && !$canModerate) {
            return $this->permissionError();
        }
        if (Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::isModule('privacy') && !Phpfox::getService('privacy')->check('pages', $page['page_id'], $page['user_id'],
                $page['privacy'], (isset($page['is_friend']) ? $page['is_friend'] : 0), true)) {
            return $this->permissionError();
        }

        list(, $members) = $this->pageService->getMembers($params['page_id'], empty($params['limit']) ? null : $params['limit'], empty($params['page']) ? 1 : $params['page'], $params['q']);
        $this->processRows($members);

        return $this->success($members);
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    function findOne($params)
    {
        $id = $this->resolver->resolveId($params);
        return $this->findAll(['page_id' => $id]);
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    function create($params)
    {
        $params = $this->resolver->setDefined(['page_id'])
            ->setRequired(['page_id'])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getMissing());
        }
        $page = $this->pageService->getForView($params['page_id']);
        if (!$page) {
            return $this->notFoundError();
        }
        if ($this->pageService->isMember($page['page_id'])) {
            return $this->error();
        }
        if (Phpfox::getUserBy('profile_page_id') <= 0 && Phpfox::isModule('privacy') && !Phpfox::getService('privacy')->check('pages', $page['page_id'], $page['user_id'],
                $page['privacy'], (isset($page['is_friend']) ? $page['is_friend'] : 0), true)) {
            return $this->permissionError();
        }
        $result = $this->processCreate($page);
        if (is_array($result)) {
            return $this->success($result['data'], [], $result['message']);
        } else {
            return $this->error($this->getErrorMessage());
        }
    }

    private function processCreate($page)
    {
        if (Phpfox::getService('like.process')->add('pages', $page['page_id'])) {
            $pageApi = (new PageApi());
            $page = $pageApi->loadResourceById($page['page_id']);
            $pageProfileMenu = $pageApi->getProfileMenus($page['page_id']);
            return [
                'data'    => [
                    'id'            => (int)$page['page_id'],
                    'total_like'    => $page['total_like'],
                    'membership'    => PageResource::LIKED,
                    'profile_menus' => $pageProfileMenu,
                    'post_types'    => $pageApi->getPostTypes($page['page_id'])
                ],
                'message' => $this->getLocalization()->translate('liked_successfully')
            ];
        } else {
            return false;
        }
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    function update($params)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    function patchUpdate($params)
    {
        // TODO: Implement updateAll() method.
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    function delete($params)
    {
        $params = $this->resolver->setDefined(['page_id'])
            ->setRequired(['page_id'])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getMissing());
        }
        $page = $this->pageService->getForView($params['page_id']);
        if (!$page) {
            return $this->notFoundError();
        }
        if (!$this->pageService->isMember($page['page_id'])) {
            return $this->error();
        }
        if (Phpfox::getService('like.process')->delete('pages', $params['page_id'])) {
            $pageApi = (new PageApi());
            $page = $pageApi->loadResourceById($page['page_id']);
            $pageProfileMenu = $pageApi->getProfileMenus($page['page_id']);
            return $this->success([
                'id'            => (int)$page['page_id'],
                'total_like'    => $page['total_like'],
                'membership'    => PageResource::NO_LIKE,
                'profile_menus' => $pageProfileMenu,
                'post_types'    => $pageApi->getPostTypes($page['page_id'])
            ], [], $this->getLocalization()->translate('un_liked_successfully'));
        }
        return $this->permissionError();
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    function form($params = [])
    {
        // TODO: Implement form() method.
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function loadResourceById($id, $returnResource = false)
    {
        // TODO: Implement loadResourceById() method.
    }

    public function processRow($item)
    {
        return PageMemberResource::populate($item)
            ->setExtra([
                'can_view_remove_friend_link' => $this->getSetting()->getUserSetting('friend.link_to_remove_friend_on_profile')
            ])
            ->displayShortFields()->toArray();
    }

    /**
     * Create custom access control layer
     */
    public function createAccessControl()
    {
        $this->accessControl =
            new GroupAccessControl($this->getSetting(), $this->getUser());
    }

    public function getRouteMap()
    {
        $resource = str_replace('-', '_', PageMemberResource::RESOURCE_NAME);
        $module = 'page';
        return [
            [
                'path'      => 'pages/:id/members',
                'routeName' => ROUTE_MODULE_DETAIL,
                'defaults'  => [
                    'moduleName'   => $module,
                    'resourceName' => $resource,
                ]
            ],
            [
                'path'      => 'pages, pages/',
                'routeName' => ROUTE_MODULE_HOME,
                'defaults'  => [
                    'moduleName'   => $module,
                    'resourceName' => 'page_home',
                ]
            ]
        ];
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