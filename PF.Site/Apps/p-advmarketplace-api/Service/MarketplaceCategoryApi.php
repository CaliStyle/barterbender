<?php


namespace Apps\P_AdvMarketplaceAPI\Service;

use Apps\P_AdvMarketplace\Service\Category\Category;
use Apps\P_AdvMarketplace\Service\Category\Process;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceCategoryResource;
use Apps\P_AdvMarketplaceAPI\Api\Security\MarketplaceAccessControl;
use Phpfox;

class MarketplaceCategoryApi extends AbstractResourceApi
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
        $this->categoryService = Phpfox::getService('advancedmarketplace.category');
        $this->processService = Phpfox::getService('advancedmarketplace.category.process');
    }

    function findAll($params = [])
    {
        $this->denyAccessUnlessGranted(MarketplaceAccessControl::VIEW);
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
        return $this->success(MarketplaceCategoryResource::populate($category)->toArray());
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
            ->from(':advancedmarketplace_category')
            ->where('category_id = ' . (int)$id)
            ->execute('getSlaveRow');

        return $category;
    }

    public function getByListingId($listingId)
    {
        $items = $this->database()->select('ac.*')
            ->from(Phpfox::getT('advancedmarketplace'), 'a')
            ->join(':advancedmarketplace_category_data', 'acd', 'a.listing_id = acd.listing_id')
            ->join(Phpfox::getT('advancedmarketplace_category'),'ac', 'ac.category_id = acd.category_id')
            ->where([
                'a.listing_id' => (int)$listingId
            ])
            ->execute('getSlaveRows');
        $result = array_map(function ($item) {
            return MarketplaceCategoryResource::populate($item)->displayShortFields()->toArray();
        }, $items);

        return $result;
    }

    public function processRow($item)
    {
        return MarketplaceCategoryResource::populate($item)->displayShortFields()->toArray();
    }
}