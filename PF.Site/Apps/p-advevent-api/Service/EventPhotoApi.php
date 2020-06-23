<?php


namespace Apps\P_AdvEventAPI\Service;

use Phpfox;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Service\NameResource;

use Apps\P_AdvEventAPI\Api\Form\EventPhotoForm;
use Apps\P_AdvEventAPI\Api\Resource\EventPhotoResource;
use Apps\P_AdvEventAPI\Api\Resource\EventResource;
use Apps\P_AdvEventAPI\Api\Security\EventAccessControl;

class EventPhotoApi extends AbstractResourceApi
{
    private $processService;
    private $eventService;

    public function __construct()
    {
        parent::__construct();
        $this->processService = Phpfox::getService('fevent.process');
        $this->eventService = Phpfox::getService('fevent');
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
        $eventId = $this->resolver->resolveId($params);
        if (!empty($eventId) && $event = $this->eventService->getEvent($eventId)) {
            $this->denyAccessUnlessGranted(EventAccessControl::MANAGE_PHOTO, EventResource::populate($event));
            $form = $this->createForm(EventPhotoForm::class);
            if (isset($event['isrepeat']) && $event['isrepeat'] > -1) {
                $form->setRecurring(true);
            }
            if ($form->isValid() && $values = $form->getValues()) {
                $values['event_id'] = $eventId;
                $values['isrepeat'] = $event['isrepeat'];
                $values['org_event_id'] = $event['org_event_id'];
                if ($this->processUpdate($values)) {
                    return $this->success([
                        'resource_name' => EventResource::populate([])->getResourceName(),
                        'id' => $eventId
                    ]);
                }
            }
            return $this->error($this->getErrorMessage());
        }
        return $this->notFoundError();
    }

    private function processUpdate($values)
    {
        if (empty($values['files'])) {
            return true;
        }
        $eventId = $values['event_id'];
        $files = $values['files'];
        $maxFiles = $this->getFileLimit(null, $eventId);
        $lastId = 0;

        if (empty($files['default']) || (!empty($files['remove']) && in_array($files['default'], $files['remove']))) {
            if (empty($files['new']) || !is_array($files['new'])) {
                return $this->error(_p('cannot_delete_default_photo'));
            } else {
                $files['default'] = $files['new'][0];
            }
        }

        // add newly added files
        if (!empty($files['new'])) {
            if (isset($files['remove'])) {
                $maxFiles += $values['remove'];
            }
            if ($maxFiles < count($files['new'])) {
                return $this->permissionError($this->getLocalization()->translate('maximum_photos_you_can_upload_is_number', ['number' => $maxFiles]));
            }
            $aRecurringImages = [];
            $tempImages = db()->select('*')
                ->from(Phpfox::getT('temp_file'))
                ->where('file_id IN (' . implode(',', $files['new']) . ')')
                ->execute('getSlaveRows');
            foreach ($tempImages as $tempImage) {
                if (!empty($tempImage)) {
                    $lastId = $newIds[] = db()->insert(':fevent_image', array(
                        'event_id' => $eventId,
                        'image_path' => $tempImage['path'],
                        'server_id' => $tempImage['server_id']
                    ));
                    $aRecurringImages[] = $tempImage['path'];
                    Phpfox::getService('core.temp-file')->delete($tempImage['file_id']);
                }
            }
            if (isset($values['isrepeat']) && ($values['isrepeat'] > -1) && !empty($values['ynfevent_editconfirmboxoption_value'])) {
                switch ($values['ynfevent_editconfirmboxoption_value']) {
                    case 'all_events_uppercase':
                        $events = $this->eventService->getBrotherEventByEventId($eventId, $values['org_event_id']);
                        foreach ($events as $key => $value) {
                            $this->processService->copyRecurringImage($value['event_id'], ['recurring_image' => $aRecurringImages], true);
                        }
                        break;

                    case 'following_events':
                        $aConds = array();
                        $aConds[] = ' AND e.event_id > ' . (int)$eventId;
                        $events = $this->eventService->getBrotherEventByEventId($eventId, $values['org_event_id'], $aConds);
                        foreach ($events as $key => $value) {
                            $this->processService->copyRecurringImage($value['event_id'], ['recurring_image' => $aRecurringImages], true);
                        }
                        break;
                }
            }
        }

        // update ordring
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
                    db()->update(':fevent_image', ['ordering' => $i], 'image_id = ' . (int)$imageId);
                    $i++;
                }
            }
        }

        // remove checked as deleted files
        if (!empty($files['remove'])) {
            //Remove image
            foreach ($files['remove'] as $imageId) {
                $imageObject = $this->loadResourceById($imageId, false);
                if ($imageObject) {
                    $this->processService->deleteImage($imageId);
                }
                //Can't set removed photo as default
                if ($files['default'] == $imageId) {
                    $files['default'] = 0;
                }
            }
        }

        // set event image from default image
        if (!empty($files['default'])) {
            $imageObject = $this->loadResourceById($files['default']);
            if (!empty($imageObject)) {
                $this->database()->update(':fevent', ['image_path' => $imageObject['image_path'], 'server_id' => $imageObject['server_id']], 'event_id = ' . (int)$eventId);
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
        $eventId = $this->resolver->resolveId($params);
        if (!empty($eventId) && $event = $this->eventService->getEvent($eventId)) {
            $this->denyAccessUnlessGranted(EventAccessControl::EDIT, EventResource::populate($event));
            $images = $this->getImages($eventId, $event['image_path']);
            $form = $this->createForm(EventPhotoForm::class, [
                'title' => 'manage_photos',
                'action' => UrlUtility::makeApiUrl('fevent-photo/:id', $eventId),
                'method' => 'PUT'
            ]);
            if (isset($event['isrepeat']) && $event['isrepeat'] > -1) {
                $form->setRecurring(true);
            }
            $form->setMaxFiles($this->getFileLimit(count($images)));
            if (!empty($images)) {
                $form->assignValues($images);
            }

            return $this->success($form->getFormStructure());
        }
        return $this->notFoundError();
    }

    private function getFileLimit($current = 0, $eventId = null)
    {
        if (empty($current) && !empty($eventId)) {
            $current = $this->eventService->countImages($eventId);
        }
        $limit = (int)$this->setting->getUserSetting('fevent.max_upload_image_event');
        return $limit - (int)$current;
    }

    private function getImages($eventId, $mainPath)
    {
        $images = $this->eventService->getImages($eventId);
        $result = [];
        if (!empty($images)) {
            foreach ($images as $image) {
                $image['main'] = !empty($mainPath) && ($mainPath === $image['image_path']) ? true : false;
                $result[] = EventPhotoResource::populate($image)->toArray();
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
        $item = db()->select('image_id, event_id, image_path, server_id')
            ->from(Phpfox::getT('fevent_image'))
            ->where('image_id = ' . (int)$id)
            ->execute('getSlaveRow');

        if (!empty($item)) {
            return $returnResource ? $this->populateResource(EventPhotoResource::class, $item) : $item;
        }

        $event = NameResource::instance()->getApiServiceByResourceName(EventResource::RESOURCE_NAME)->loadResourceById($item['id']);
        $item['is_primary'] = ($event['image_path'] == $item['image_path']) ? 1 : 0;
        return null;
    }

    /**
     * Create custom access control layer
     */
    public function createAccessControl()
    {
        $this->accessControl =
            new EventAccessControl($this->getSetting(), $this->getUser());
    }

}