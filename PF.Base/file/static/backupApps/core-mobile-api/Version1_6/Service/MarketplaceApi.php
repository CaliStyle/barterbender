<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Version1_6\Service;

use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\Resource\MarketplaceResource;
use Apps\Core_MobileApi\Api\Security\Marketplace\MarketplaceAccessControl;
use Apps\Core_MobileApi\Version1_6\Api\Form\Marketplace\MarketplaceForm;
use Phpfox;

class MarketplaceApi extends \Apps\Core_MobileApi\Service\MarketplaceApi
{
    function form($params = [])
    {
        $editId = $this->resolver->resolveSingle($params, 'id');
        /** @var MarketplaceForm $form */
        $form = $this->createForm(MarketplaceForm::class, [
            'title'  => 'create_a_listing',
            'action' => UrlUtility::makeApiUrl('marketplace'),
            'method' => 'POST'
        ]);
        $form->setCategories($this->getCategories());
        $form->setCurrencies($this->getCurrencies());
        $listing = $this->loadResourceById($editId, true);
        if ($editId && empty($listing)) {
            return $this->notFoundError();
        }

        if ($listing) {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::EDIT, $listing);
            $form->setEditing(true);
            $form->setTitle('edit_listing')
                ->setAction(UrlUtility::makeApiUrl('marketplace/:id', $editId))
                ->setMethod('PUT');
            $form->assignValues($listing);
        } else {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::ADD);
            if (($iFlood = $this->getSetting()->getUserSetting('marketplace.flood_control_marketplace')) !== 0) {
                $aFlood = [
                    'action' => 'last_post', // The SPAM action
                    'params' => [
                        'field'      => 'time_stamp', // The time stamp field
                        'table'      => Phpfox::getT('marketplace'), // Database table we plan to check
                        'condition'  => 'user_id = ' . $this->getUser()->getId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    ]
                ];

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {
                    return $this->error($this->getLocalization()->translate('you_are_creating_a_listing_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
        }

        return $this->success($form->getFormStructure());
    }

    function create($params)
    {
        $this->denyAccessUnlessGranted(MarketplaceAccessControl::ADD);
        /** @var MarketplaceForm $form */
        $form = $this->createForm(MarketplaceForm::class);
        if ($form->isValid()) {
            $values = $form->getValues();
            $id = $this->processCreate($values);
            if ($id) {
                return $this->success([
                    'id'            => $id,
                    'resource_name' => MarketplaceResource::populate([])->getResourceName(),
                    'editing'       => true
                ]);
            } else {
                return $this->error($this->getErrorMessage());
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
    }


    function update($params)
    {
        $id = $this->resolver->resolveId($params);
        /** @var MarketplaceForm $form */
        $form = $this->createForm(MarketplaceForm::class);
        $form->setEditing(true);
        $listing = $this->loadResourceById($id, true);
        if (empty($listing)) {
            return $this->notFoundError();
        }
        $this->denyAccessUnlessGranted(MarketplaceAccessControl::EDIT, $listing);
        if ($form->isValid() && ($values = $form->getValues())) {
            $values['view_id'] = $listing->view_id;
            $success = $this->processUpdate($id, $values);
            if ($success) {
                return $this->success([
                    'id'            => $id,
                    'resource_name' => MarketplaceResource::populate([])->getResourceName()
                ]);
            } else {
                return $this->error($this->getErrorMessage());
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
    }
}