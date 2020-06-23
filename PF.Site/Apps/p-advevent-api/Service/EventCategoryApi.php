<?php


namespace Apps\P_AdvEventAPI\Service;

use Apps\P_AdvEvent\Service\Category\Category;
use Apps\P_AdvEvent\Service\Category\Process;

use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\P_AdvEventAPI\Api\Resource\EventCategoryResource;
use Apps\P_AdvEventAPI\Api\Security\EventAccessControl;
use Phpfox;

class EventCategoryApi extends AbstractResourceApi
{
    /**
     * @var Category
     */
    private $categoryService;

    /**
     * @var Process
     */
    private $processService;

    public function __construct()
    {
        parent::__construct();
        $this->categoryService = Phpfox::getService('fevent.category');
        $this->processService = Phpfox::getService('fevent.category.process');
    }

    function findAll($params = [])
    {
        $this->denyAccessUnlessGranted(EventAccessControl::VIEW);

        $result = $this->categoryService->getForBrowse();
        $this->processRows($result);

        return $this->success($result);
    }

    function findOne($params)
    {
        $id = $this->resolver->resolveId($params);
        $category = $this->loadResourceById($id);
        if (empty($category)) {
            return $this->notFoundError();
        }
        return $this->success(EventCategoryResource::populate($category)->toArray());
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
        // TODO: Implement delete() method.
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
        $category = $this->database()->select('*')
            ->from(':fevent_category')
            ->where('category_id = ' . (int)$id)
            ->execute('getSlaveRow');

        return $category;
    }

    public function getByEventId($id)
    {
        $where = ['AND cd.event_id = ' . (int)$id];
        $result = $this->database()->select('c.*')
            ->from(':fevent_category', 'c')
            ->join(':fevent_category_data', 'cd', 'c.category_id = cd.category_id')
            ->where($where)
            ->order('c.parent_id ASC')
            ->group('c.category_id')
            ->execute('getRows');

        $result = array_map(function ($item) {
            return EventCategoryResource::populate($item)->displayShortFields()->toArray();
        }, $result);

        return $result;
    }

    public function processRow($item)
    {
        return EventCategoryResource::populate($item)->displayShortFields()->toArray();
    }
}