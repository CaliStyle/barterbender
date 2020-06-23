<?php
namespace Apps\P_AdvMarketplaceAPI\Service;

use Apps\P_AdvMarketplaceAPI\Api\Form\MarketplacePhotoForm;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplacePhotoResource;
use Phpfox;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceResource;
use Apps\P_AdvMarketplaceAPI\Api\Security\MarketplaceAccessControl;

class MarketplacePhotoApi extends AbstractResourceApi
{
    private $processService;
    private $listingService;

    public function __construct()
    {
        parent::__construct();
        $this->processService = Phpfox::getService('advancedmarketplace.process');
        $this->listingService = Phpfox::getService('advancedmarketplace');
    }

    /**
     * Get list of documents, filter by
     *
     * @param array $params
     * @return array|mixed
     * @throws \Exception
     */
    public function findAll($params = [])
    {
        return null;
    }

    /**
     * Find detail one document
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function findOne($params)
    {
        return null;
    }

    /**
     * Create new document
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function create($params)
    {
        return null;
    }

    /**
     * Update existing document
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function update($params)
    {
        $listingId = $this->resolver->resolveId($params);
        if(!empty($listingId) && $item = $this->listingService->getListing($listingId))
        {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::MANAGE_PHOTO, MarketplaceResource::populate($item));
            $form = $this->createForm(MarketplacePhotoForm::class);
            if($form->isValid() && $values = $form->getValues()) {
                $values['listing_id'] = $listingId;
                if($this->processUpdate($values)) {
                    return $this->success([
                        'resource_name' => MarketplaceResource::populate([])->getResourceName(),
                        'id' => $listingId
                    ]);
                }
            }
            return $this->error($this->getErrorMessage());
        }
        return $this->notFoundError();
    }

    private function processUpdate($values)
    {
        if(empty($values['files'])) {
            return true;
        }

        $listingId = $values['listing_id'];
        $files = $values['files'];
        $maxFiles = $this->getFileLimit(null, $listingId);
        $lastId = 0;

        if(!empty($files['remove']) && in_array($files['default'], $files['remove'])) {
            if (empty($files['new']) || !is_array($files['new'])) {
                return $this->permissionError($this->getLocalization()->translate('advancedmarketplace_api_cannot_delete_default_photo'));
            } else {
                $files['default'] = $files['new'][0];
            }
        }

        if(!empty($files['new'])) {
            if(isset($files['remove'])) {
                $maxFiles += count($files['remove']);
            }
            if($maxFiles < count($files['new'])) {
                return $this->permissionError($this->getLocalization()->translate('maximum_photos_you_can_upload_is_number', ['number' => $maxFiles]));
            }
            $tempImages = db()->select('*')->from(Phpfox::getT('temp_file'))->where('file_id IN ('. implode(',', $files['new']) .')')->execute('getSlaveRows');
            foreach($tempImages as $tempImage)
            {
                if(!empty($tempImage)) {
                    $lastId = $newIds[] = db()->insert(':advancedmarketplace_image', array(
                        'listing_id' => $listingId,
                        'image_path' => $tempImage['path'],
                        'server_id' => $tempImage['server_id']
                    ));
                    Phpfox::getService('core.temp-file')->delete($tempImage['file_id']);
                }
            }
        }

        if (!empty($files['order'])) {
            $i = 1;
            if (count($newIds)) {
                $orderList = array_merge($files['order'], $newIds);
            } else {
                $orderList = $files['order'];
            }
            foreach ($orderList as $imageId) {
                $imageObject = $this->loadResourceById($imageId, false);
                if ($imageObject) {
                    db()->update(':advancedmarketplace_image', ['ordering' => $i], 'image_id = ' . (int)$imageId);
                    $i++;
                }
            }
        }
        if (!empty($files['remove'])) {
            //Remove image
            foreach($files['remove'] as $imageId) {
                $imageObject = $this->loadResourceById($imageId, false);
                if ($imageObject) {
                    $this->processService->deleteImage($imageId);
                }
            }
        }
        if (!empty($files['default'])) {
            $imageObject = $this->loadResourceById($files['default']);
            if (!empty($imageObject)) {
                $this->database()->update(':advancedmarketplace',['image_path' => $imageObject['image_path'],'server_id' => $imageObject['server_id']],'listing_id = '.(int)$listingId);
            }
        } elseif ($lastId) {
            //Check set default
            $this->processService->setDefault($lastId);
        }
        return true;
    }

    /**
     * Update multiple document base on document query
     *
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function patchUpdate($params)
    {
        return null;
    }

    /**
     * Delete a document
     * DELETE: /resource-name/:id
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function delete($params)
    {
        return null;
    }

    /**
     * Get Create/Update document form
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function form($params = [])
    {
        $listingId = $this->resolver->resolveId($params);
        if(!empty($listingId) && $item = $this->listingService->getListing($listingId))
        {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::EDIT, MarketplaceResource::populate($item));
            $images = $this->getImages($listingId, $item['image_path']);
            $form = $this->createForm(MarketplacePhotoForm::class, [
                'title' => 'edit_listing',
                'action' => UrlUtility::makeApiUrl('advancedmarketplace-photo/:id', $listingId),
                'method' => 'PUT'
            ]);
            $form->setMaxFiles($this->getFileLimit(count($images)));
            if (!empty($images)) {
                $form->assignValues($images);
            }

            return $this->success($form->getFormStructure());
        }
        return $this->notFoundError();
    }

    private function getFileLimit($current = 0, $listingId = null)
    {
        if(empty($current) && !empty($listingId)) {
            $current = $this->listingService->countImages($listingId);
        }
        $limit = (int)$this->setting->getUserSetting('advancedmarketplace.total_photo_upload_limit');
        return $limit - (int)$current;
    }

    private function getImages($listingId, $mainPath)
    {
        $images = $this->listingService->getImages($listingId);
        $result = [];
        if(!empty($images)) {
            foreach($images  as $image) {
                $image['main'] = !empty($mainPath) && ($mainPath === $image['image_path']) ? true : false;
                $result[] = MarketplacePhotoResource::populate($image)->toArray();
            }
            return $result;
        }
        return [];
    }


    /**
     * Approve pending item
     * PUT: /resource-name/approve/:id
     * @param $params
     * @return mixed
     */
    public function approve($params)
    {
        return null;
    }

    /**
     * Feature / Un-feature item
     * PUT: /resource-name/feature/:id
     * @param $params
     * @return mixed
     */
    public function feature($params)
    {
        return null;
    }

    /**
     * Sponsor / Un-sponsor item
     * PUT: /resource-name/sponsor/:id
     * @param $params
     * @return mixed
     */
    public function sponsor($params)
    {
        return null;
    }

    public function loadResourceById($id, $returnResource = false)
    {
        $item = db()->select('image_id, listing_id, image_path, server_id, is_primary')
                    ->from(Phpfox::getT('advancedmarketplace_image'))
                    ->where('image_id = '. (int)$id)
                    ->execute('getSlaveRow');
        if(!empty($item)) {
            return $returnResource ? $this->populateResource(MarketplacePhotoResource::class, $item) : $item;
        }
        return null;
    }

    /**
     * Create custom access control layer
     */
    public function createAccessControl()
    {
        $this->accessControl =
            new MarketplaceAccessControl($this->getSetting(), $this->getUser());
    }

}