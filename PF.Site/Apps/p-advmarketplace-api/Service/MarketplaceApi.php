<?php
/**
 * @author  OvalSky
 * @license phpfox.com
 */

namespace Apps\P_AdvMarketplaceAPI\Service;

use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceInviteResource;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplacePhotoResource;
use Apps\P_AdvMarketplaceAPI\Api\Security\MarketplaceAccessControl;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceResource;
use Apps\P_AdvMarketplaceAPI\Api\Resource\MarketplaceCategoryResource;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileApp;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileAppSettingInterface;
use Apps\Core_MobileApi\Adapter\MobileApp\Screen;
use Apps\Core_MobileApi\Adapter\MobileApp\ScreenSetting;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Api\ActivityFeedInterface;
use Apps\P_AdvMarketplaceAPI\Api\Form\MarketplaceForm;
use Apps\Core_MobileApi\Api\Resource\Object\HyperLink;
use Apps\Core_MobileApi\Service\Helper\Pagination;
use Apps\Core_MobileApi\Api\Security\AppContextFactory;
use Phpfox;
use Phpfox_Database;
use Apps\Core_MobileApi\Service\NameResource;
use Apps\Core_MobileApi\Api\Resource\TagResource;

class MarketplaceApi extends AbstractResourceApi implements MobileAppSettingInterface
{
    private $categoryService;
    private $helperService;
    private $processService;
    private $listingService;

    private $browserService;
    private $userService;

    public function __construct()
    {
        parent::__construct();
        $this->categoryService = Phpfox::getService('advancedmarketplace.category');
        $this->helperService = Phpfox::getService('advancedmarketplace.helper');
        $this->processService = Phpfox::getService('advancedmarketplace.process');
        $this->listingService = Phpfox::getService('advancedmarketplace');
        $this->browserService = Phpfox::getService('advancedmarketplace.browse');
        $this->userService = Phpfox::getService('user');
    }

    public function __naming()
    {
        return [
            'advancedmarketplace/wishlist/:id' => [
                'put' => 'wishlist'
            ],
        ];
    }

    public function getAppSetting($param)
    {
        $l = $this->getLocalization();
        $app = new MobileApp('advancedmarketplace', [
            'title' => $l->translate('menu_advancedmarketplace'),
            'home_view' => 'menu',
            'main_resource' => new MarketplaceResource([]),
            'category_resource' => new MarketplaceCategoryResource([]),
            'other_resources'=> [
                new MarketplacePhotoResource([]),
                new MarketplaceInviteResource([])
            ],
        ]);
        $resourceName = (new MarketplaceResource([]))->getResourceName();

        $headerButtons[$resourceName] = [
            [
                'icon' => 'list-bullet-o',
                'action' => Screen::ACTION_FILTER_BY_CATEGORY,
            ],
        ];

        if ($this->getAccessControl()->isGranted(MarketplaceAccessControl::ADD)) {
            $headerButtons[$resourceName][] = [
                'icon' => 'plus',
                'action' => Screen::ACTION_ADD,
                'params' => [
                    'module_name' => 'advancedmarketplace',
                    'resource_name' => $resourceName
                ]
            ];
        }
        $app->addSetting('home.header_buttons', $headerButtons);

        return $app;
    }

    /**
     * Create custom access control layer
     */
    public function createAccessControl()
    {
        $this->accessControl =
            new MarketplaceAccessControl($this->getSetting(), $this->getUser());
        $moduleId = $this->request()->get('module_id');
        $itemId = $this->request()->get('item_id');

        if ($moduleId && $itemId) {
            $context = AppContextFactory::create($moduleId, $itemId);
            if ($context === null) {
                return $this->notFoundError();
            }
            $this->accessControl->setAppContext($context);
        }
    }

    function findAll($params = [])
    {
        $params = $this->resolver->setDefined(['view', 'category', 'q', 'sort', 'profile_id', 'module_id', 'item_id', 'limit', 'page', 'when', 'location', 'tag'])
            ->setAllowedValues('sort', ['latest', 'most_viewed', 'most_liked', 'most_discussed', 'low_high_price', 'high_low_price', 'featured', 'sponsored'])
            ->setAllowedValues('view', ['my', 'pending', 'friend', 'invites', 'expired', 'sold', 'my-wishlist'])
            ->setAllowedValues('when', ['all-time', 'today', 'this-week', 'this-month'])
            ->setAllowedTypes('limit', 'int', [
                'min' => Pagination::DEFAULT_MIN_ITEM_PER_PAGE,
                'max' => Pagination::DEFAULT_MAX_ITEM_PER_PAGE
            ])
            ->setAllowedTypes('category', 'int')
            ->setAllowedTypes('page', 'int')
            ->setAllowedTypes('profile_id', 'int')
            ->setDefault([
                'limit' => Pagination::DEFAULT_ITEM_PER_PAGE,
                'page' => 1
            ])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getInvalidParameters());
        }

        if (!$this->getAccessControl()->isGranted(MarketplaceAccessControl::VIEW)) {
            return $this->permissionError();
        }

        $sort = $params['sort'];
        $view = $params['view'];
        $when = $params['when'];

        $isProfile = $params['profile_id'];

        if (in_array($view, ['feature', 'sponsor'])) {
            $function = 'find' . ucfirst($view);
            return $this->success($this->{$function}($params));
        }

        $parentModule = null;
        if (!empty($params['module_id']) && !empty($params['item_id'])) {
            $parentModule = [
                'module_id' => $params['module_id'],
                'item_id'   => $params['item_id'],
            ];
        }

        $user = [];
        if ($isProfile) {
            $user = $this->userService->get($isProfile);
            if (empty($user)) {
                return $this->notFoundError();
            }
        }
        $this->search()->setBIsIgnoredBlocked(true);
        $browseParams = [
            'module_id' => 'advancedmarketplace',
            'alias' => 'l',
            'field' => 'listing_id',
            'table' => Phpfox::getT('advancedmarketplace'),
            'hide_view' => ['pending', 'my'],
            'service' => 'advancedmarketplace.browse',
        ];

        // Search By tag
        if (!empty($params['tag'])) {
            if ($aTag = Phpfox::getService('tag')->getTagInfo('advancedmarketplace', $params['tag'])) {
                $this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($aTag['tag_text']) . '\' AND tag.tag_type = 0');
                $this->browserService->setIsTagSearching(true);
            } else {
                $this->search()->setCondition('AND 0');
            }
        }

        // sort
        switch ($sort) {
            case 'most_viewed':
                $sort = 'l.total_view DESC';
                break;
            case 'most_liked':
                $sort = 'l.total_like DESC';
                break;
            case 'most_discussed':
                $sort = 'l.total_comment DESC';
                break;
            case 'low_high_price':
                $sort = 'l.price ASC';
                break;
            case 'high_low_price':
                $sort = 'l.price DESC';
                break;
            case 'featured':
                $this->search()->setCondition(' AND l.is_featured = 1');
                $sort = 'l.time_stamp DESC';
                break;
            case 'sponsored':
                $this->search()->setCondition(' AND l.is_sponsor = 1');
                $sort = 'l.time_stamp DESC';
                break;
            case 'latest':
            default:
                $sort = 'l.time_stamp DESC';
                break;
        }

        switch ($view) {
            case 'sold':
                if (Phpfox::isUser()) {
                    $this->search()->setCondition('AND l.user_id = ' . Phpfox::getUserId());
                    $this->search()->setCondition('AND l.is_sell = 1');
                } else {
                    return $this->permissionError();
                }
                break;
            case 'my':
                if (Phpfox::isUser()) {
                    $this->search()->setCondition('AND l.user_id = ' . Phpfox::getUserId());
                } else {
                    return $this->permissionError();
                }
                break;
            case 'pending':
                if (Phpfox::getUserParam('advancedmarketplace.can_approve_listings')) {
                    $this->search()->setCondition('AND l.view_id = 1 AND post_status != 2 ');
                } else {
                    if ($isProfile) {
                        $this->search()->setCondition("AND l.view_id IN(" . ($user['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ") AND l.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($user)) . ") AND l.user_id = " . $user['user_id'] . "");
                    } else {
                        return $this->permissionError();
                    }
                }
                break;
            case 'expired':
                $this->search()->setCondition('AND l.has_expiry = 1 AND l.expiry_date < ' . PHPFOX_TIME);
                break;
            default:
                if ($isProfile) {
                    $this->search()->setCondition("AND l.post_status != 2 AND l.view_id IN(" . ($user['user_id'] == Phpfox::getUserId() ? '0,1' : '0') . ") AND l.privacy IN(" . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('core')->getForBrowse($user)) . ") AND l.user_id = " . $user['user_id'] . " AND (l.has_expiry = 0 OR l.expiry_date > ". PHPFOX_TIME . ")");
                } elseif ($parentModule == null) {
                    switch ($view) {
                        case 'invites':
                            Phpfox::isUser(true);
                            $this->browserService->seen();
                            break;
                    }
                    $this->search()->setCondition('AND l.view_id = 0 AND l.privacy IN(%PRIVACY%)'. ($view != 'my-wishlist' ? ' AND l.post_status = 1  AND (l.has_expiry = 0 OR l.expiry_date > ' . PHPFOX_TIME . ')' : ''));
                } elseif ($parentModule != null) {
                    $this->search()->setCondition('AND l.post_status != 2 AND l.module_id = \'' . $parentModule['module_id'] . '\' AND l.item_id = ' . (int)$parentModule['item_id']);
                }
                break;
        }
        //location
        if ($params['location']) {
            $this->search()->setCondition('AND l.country_iso = \'' . Phpfox_Database::instance()->escape($params['location']) . '\'');
        }
        // search
        if (!empty($params['q'])) {
            $this->search()->setCondition('AND l.title LIKE "' . Phpfox::getLib('parse.input')->clean('%' . $params['q'] . '%') . '"');
        }
        //category
        if ($params['category']) {
            $this->browserService->category($params['category']);
            $strChildIds = rtrim($params['category'] . ',' . $this->categoryService->getChildIds($params['category']), ',');
            $this->search()->setCondition(' AND (mcd.category_id IN (' . $strChildIds . '))');
        }
        //when
        if ($when) {
            $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
            switch ($when) {
                case 'today':
                    $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                    $this->search()->setCondition(' AND (l.time_stamp >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND l.time_stamp < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')');
                    break;
                case 'this-week':
                    $this->search()->setCondition(' AND l.time_stamp >= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart()));
                    $this->search()->setCondition(' AND l.time_stamp <= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd()));
                    break;
                case 'this-month':
                    $this->search()->setCondition(' AND l.time_stamp >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'');
                    $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                    $this->search()->setCondition(' AND l.time_stamp <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'');
                    break;
                case 'all-time':
                default:
                    break;
            }
        }
        $this->search()->setSort($sort)->setLimit($params['limit'])->setPage($params['page']);

        $this->browse()->params($browseParams)->execute();

        $items = $this->browse()->getRows();

        $this->processRows($items);
        return $this->success($items);
    }

    function findOne($params)
    {
        $listingId = $this->resolver->resolveId($params);
        if(!empty($listingId) && $item = $this->loadResourceById($listingId))
        {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::VIEW, MarketplaceResource::populate($item));

            if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $item['user_id'])) {
                return $this->permissionError();
            }
            if (Phpfox::isModule('privacy') && !Phpfox::getService('privacy')->check('marketplace', $item['listing_id'], $item['user_id'],
                    $item['privacy'], $item['is_friend'], true)) {
                return $this->permissionError();
            }
            if ($item['post_status'] == 2 && !($item['user_id'] == phpfox::getUserId() || $this->setting->getUserSetting('advancedmarketplace.can_view_draft_listings'))) {
                return $this->permissionError();
            }


            // Increment the view counter
            $updateCounter = false;
            $trackObject = Phpfox::getService('track.process');
            if (Phpfox::isModule('track')) {
                if (!$item['is_viewed']) {
                    $updateCounter = true;
                    $trackObject->add('advancedmarketplace', $item['listing_id']);
                } else {
                    if (!setting('track.unique_viewers_counter')) {
                        $updateCounter = true;
                        $trackObject->add('advancedmarketplace', $item['listing_id']);
                    } else {
                        $trackObject->update('advancedmarketplace', $item['listing_id']);
                    }
                }
            } else {
                $updateCounter = true;
            }

            if ($updateCounter) {
                $this->processService->updateViewCounter($item['listing_id']);
                $item['total_view']++;
            }

            if ($item['invite_id'] && !$item['visited_id'] && $item['user_id'] != Phpfox::getUserId()) {
                $this->processService->setVisit($item['listing_id'], Phpfox::getUserId());
            }

            if ($item['post_status'] == 2) {
                $item['title'] = '[' . _p('draft') .'] ' . $item['title'];
            }

            $item['is_detail'] = true;
            $item['images_list'] = $this->listingService->getImages($item['listing_id']);
            $resource = $this->populateResource(MarketplaceResource::class, $item);

            $this->setHyperlinks($resource, true);

            return $this->success($resource
                ->setExtra($this->getAccessControl()->getPermissions($resource))
                ->loadFeedParam()
                ->toArray());
        }
        return $this->notFoundError();
    }

    function create($params)
    {
        $this->denyAccessUnlessGranted(MarketplaceAccessControl::ADD);
        $form = $this->createForm(MarketplaceForm::class);
        $isInstantPayment = !!($this->resolver->resolveSingle($params, 'is_sell', null, [0,1], 0));
        $form->setIsPaymentMethodRequired($isInstantPayment);
        if($isInstantPayment) {
            $paymentGateways = $this->_getPaymentGateways();
            $form->setPaymentMethods($paymentGateways);
        }
        if ($form->isValid()) {
            $values = $form->getValues();
            if($isInstantPayment && empty($values['payment_methods'][0])) {
                $form->setInvalidField('payment_methods', $this->getLocalization()->translate('field_name_field_is_invalid', [
                    'field_name' => $this->getLocalization()->translate('payment_methods'),
                ]));
                return $this->validationParamsError($form->getInvalidFields());
            }
            $id = $this->processCreate($values);
            if (!empty($id)) {
                return $this->success([
                    'id' => $id,
                    'resource_name' => (new MarketplaceResource([]))->getResourceName(),
                ]);
            } else {
                return $this->error($this->getErrorMessage());
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
    }

    private function processCreate($values, $extraParams = [])
    {
        $data = [
            'privacy' => $values['privacy'],
            'title' => $values['title'],
            'currency_id' => $values['currency_id'],
            'price' => $values['price'],
            'country_iso' => $values['country_iso'],
            'country_child_id' => isset($values['country_child_id']) ? $values['country_child_id'] : 0,
            'postal_code' => isset($values['postal_code']) ? $values['postal_code'] : null,
            'city' => !empty($values['city']) ? $values['city'] : null,
            'is_sell' => !empty($values['is_sell']) ? $values['is_sell'] : 0,
            'auto_sell' => !empty($values['auto_sell']) ? $values['auto_sell'] : 0,
            'tag_list' => isset($values['tags']) ? $values['tags'] : null,
            'post_status' => !empty($values['is_draft']) ? 2 : 1,
            'address' => !empty($values['address']) ? $values['address'] : null,
            'location' => !empty($values['location']) ? $values['location'] : null,
            'short_description' => !empty($values['short_description']) ? Phpfox::getLib('parse.input')->clean($values['short_description'],
                200) : null,
            'description' => !empty($values['text']) ? $values['text'] : null,
            'category' => !empty($values['categories'][1]) ? $values['categories'][1] : $values['categories'][0],
            'module_id' => $values['module_id'],
            'item_id' => $values['item_id'],
            'payment_methods' => !empty($values['payment_methods'][0]) ? $values['payment_methods'] : ''
        ];

        if(!empty($values['expired_date'])) {
            $time = strtotime($values['expired_date']);
            $data = array_merge($data, [
                'has_expiry' => 1,
                'expiry_day' => Phpfox::getTime('d', $time),
                'expiry_month' => Phpfox::getTime('m', $time),
                'expiry_year' => Phpfox::getTime('Y', $time),
            ]);
        }

        if(!isset($extraParams['is_edit'])) {
            $image = Phpfox::getService('core.temp-file')->get($values['image']['temp_file']);
            if (empty($image)) {
                return $this->error(_p('error_uploading_photo'));
            } else {
                if (!Phpfox::getService('user.space')->isAllowedToUpload(Phpfox::getUserId(), $image['size'])) {
                    Phpfox::getService('core.temp-file')->delete($image['temp_file'], true);
                    return $this->error('you_are_out_of_space_to_upload_photo');
                }
                Phpfox::getService('core.temp-file')->delete($image['temp_file']);
                $data['image_path'] = $image['path'];
                $data['server_id'] = $image['server_id'];
            }
        }

        if(isset($extraParams['is_edit'])) {
            $listingId = $values['listing_id'];
            if(!empty($values['mark_sold'])) {
                $data['view_id'] = 2;
            }
            $this->processService->update($listingId, $data, $extraParams['user_id'], $extraParams['item']);
        }
        else {
            $listingId = $this->processService->add($data);
        }

        return $listingId;
    }

    function update($params)
    {
        $listingId = $this->resolver->resolveId($params);
        if(!empty($listingId) && $item = $this->loadResourceById($listingId)) {
            $resource = $this->populateResource(MarketplaceResource::class, $item);
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::EDIT, $resource);
            $form = $this->createForm(MarketplaceForm::class);
            $form->setIsEdit(true);
            $isInstantPayment = !!($this->resolver->resolveSingle($params, 'is_sell', null, [0,1], 0));
            $form->setIsPaymentMethodRequired($isInstantPayment);
            $form->setIsDraft($item['post_status'] == 2);
            if($isInstantPayment) {
                $paymentGateways = $this->_getPaymentGateways();
                $form->setPaymentMethods($paymentGateways);
            }
            if ($form->isValid()) {
                $values = $form->getValues();
                if($isInstantPayment && empty($values['payment_methods'][0])) {
                    $form->setInvalidField('payment_methods', $this->getLocalization()->translate('field_name_field_is_invalid', [
                        'field_name' => $this->getLocalization()->translate('payment_methods'),
                    ]));
                    return $this->validationParamsError($form->getInvalidFields());
                }
                $id = $this->processCreate(array_merge($values, ['listing_id' => $listingId]), ['is_edit' => true, 'user_id' => $item['user_id'], 'item' => $item]);
                if (!empty($id)) {
                    return $this->success([
                        'id' => $id,
                        'resource_name' => (new MarketplaceResource([]))->getResourceName(),
                    ],[], _p('advancedmarketplace_listing_successfully_updated'));
                }
                else {
                    return $this->error($this->getErrorMessage());
                }
            }
            else {
                return $this->validationParamsError($form->getInvalidFields());
            }
        }
        return $this->notFoundError();
    }

    function patchUpdate($params)
    {
        // TODO: Implement patchUpdate() method.
    }

    function delete($params)
    {
        $listingId = $this->resolver->resolveId($params);
        if(!empty($listingId) && $resource = $this->loadResourceById($listingId, true)) {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::DELETE, $resource);
            $success = $this->processService->delete($listingId);
            if($success) {
                return $this->success([], [], $this->getLocalization()->translate('successfully_deleted_listing'));
            }
        }
        return $this->notFoundError();
    }

    function form($params = [])
    {
        $editId = $this->resolver->resolveId($params);
        $isEdit = false;

        if(!empty($editId) && $item = $this->listingService->getForEdit($editId, true)) {
            $item['is_edit'] = $isEdit = true;
            $resource = $this->populateResource(MarketplaceResource::class, $item);
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::EDIT, $resource);
        }
        else {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::ADD);
            if (($flood = Phpfox::getUserParam('advancedmarketplace.flood_control_advancedmarketplace')) !== 0) {
                $floodParams = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'time_stamp', // The time stamp field
                        'table' => Phpfox::getT('advancedmarketplace'), // Database table we plan to check
                        'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $flood * 60 // Seconds);
                    )
                );

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($floodParams)) {
                    return $this->error($this->getLocalization()->translate('you_are_creating_a_listing_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
        }

        $form = $this->createForm(MarketplaceForm::class, [
            'title' => 'advancedmarketplace.create_new_listing',
            'method' => 'post',
            'action' => UrlUtility::makeApiUrl('advancedmarketplace')
        ]);
        $form->setCategories($this->categoryService->getForBrowse())
            ->setCurrencies($this->getLocalization()->getAllCurrencies())
            ->setIsEdit($isEdit);

        $paymentGateways = $this->_getPaymentGateways();
        $form->setPaymentMethods($paymentGateways);

        if ($isEdit) {
            $form->setAction(UrlUtility::makeApiUrl('advancedmarketplace/:id', $editId))
                ->setTitle('advancedmarketplace.editing_listing')
                ->setMethod('put');
            $form->setIsDraft($item['post_status'] == 2);
            $resource->tags = $this->getTagApi()
                ->getTagsBy(MarketplaceResource::TAG_CATEGORY, $resource->id);
            $form->assignValues($resource);
        }

        return $this->success($form->getFormStructure());
    }

    function approve($params)
    {
        $listingId = $this->resolver->resolveId($params);
        if(!empty($listingId) && $resource = $this->loadResourceById($listingId, true)) {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::APPROVE, $resource);
            if($this->processService->approve($listingId)) {
                $permission = $this->getAccessControl()->getPermissions(MarketplaceResource::populate($this->loadResourceById($listingId)));
                return $this->success(array_merge($permission, ['is_pending' => false]),[], $this->getLocalization()->translate('listing_has_been_approved'));
            }
        }
    }

    function feature($params)
    {
        $listingId = $this->resolver->resolveId($params);
        $feature = $this->resolver->resolveSingle($params, 'feature', 'int', ['min' => 0, 'max' => 1], 1);
        if(!empty($listingId) && $resource = $this->loadResourceById($listingId, true)) {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::FEATURE, $resource);
            if($this->processService->feature($listingId, $feature)) {
                return $this->success([
                    'is_featured' => !!$feature
                ], [], $feature ? $this->getLocalization()->translate('listing_successfully_featured') :  $this->getLocalization()->translate('listing_successfully_un_featured'));
            }
            return $this->error($this->getErrorMessage());
        }
        return $this->notFoundError();
    }

    function sponsor($params)
    {
        $listingId = $this->resolver->resolveId($params);
        $sponsor = (int)$this->resolver->resolveSingle($params,'sponsor','int', ['min' => 0, 'max' => 1], 1);
        if(!empty($listingId) && $resouce = $this->loadResourceById($listingId, true)) {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::SPONSOR, $resouce);
            if($this->processService->sponsor($listingId, $sponsor)) {
                if($sponsor) {
                    Phpfox::getService('ad.process')->addSponsor(array(
                        'module' => 'advancedmarketplace',
                        'item_id' => $listingId,
                        'name' => _p('advancedmarketplace_sponsor_title', array('sListingTitle' => $resouce->getTitle()))
                    ), false);
                }
                else {
                    Phpfox::getService('ad.process')->deleteAdminSponsor('advancedmarketplace', $listingId);
                }
                return $this->success([
                    'is_sponsor' => !!$sponsor
                ],[],$sponsor ? $this->getLocalization()->translate('listing_successfully_sponsored') :  $this->getLocalization()->translate('listing_successfully_un_sponsored'));
            }
            return $this->error($this->getErrorMessage());
        }
        return $this->notFoundError();
    }

    function loadResourceById($id, $returnResource = false)
    {
        $item = $this->listingService->getListing($id);
        if(empty($item)) {
            return false;
        }
        if($returnResource) {
            return $this->populateResource(MarketplaceResource::class, $item);
        }
        return $item;
    }

    public function processRow($item)
    {
        /** @var MarketplaceResource $resource */
        $resource = $this->populateResource(MarketplaceResource::class, $item);
        $this->setHyperlinks($resource);

        $view = $this->request()->get('view');
        $shortFields = [];

        if (in_array($view, ['sponsor', 'feature'])) {
            $shortFields = [
                'resource_name', 'title', 'statistic', 'image', 'id', 'price'
            ];
            if ($view == 'sponsor') {
                $shortFields[] = 'sponsor_id';
            }
        }

        return $resource->setExtra($this->getAccessControl()->getPermissions($resource))->displayShortFields()->toArray($shortFields);
    }

    public function wishlist($params)
    {
        $id = $this->resolver->resolveId($params);
        $wishlist = $this->resolver->resolveSingle($params, 'is_wishlist', 'bool', [true, false], false);
        if(!empty($id) && ($item = $this->loadResourceById($id)) && isset($wishlist)) {
            $this->denyAccessUnlessGranted(MarketplaceAccessControl::WISHLIST, MarketplaceResource::populate($item));
            if($this->processService->processWishlist($id, null, !$wishlist)) {
                return $this->success([
                    'is_wishlist' => !$wishlist
                ], [] , _p($wishlist ? 'advancedmarketplace_api_removed_from_wishlist' : 'advancedmarketplace_api_added_to_wishlist'));
            }
        }
        return $this->notFoundError();
    }


    private function setHyperlinks(MarketplaceResource $resource, $includeLinks = false)
    {
        $resource->setSelf([
            MarketplaceAccessControl::VIEW => $this->createHyperMediaLink(MarketplaceAccessControl::VIEW, $resource,
                HyperLink::GET, 'advancedmarketplace/:id', ['id' => $resource->getId()]),
            MarketplaceAccessControl::EDIT => $this->createHyperMediaLink(MarketplaceAccessControl::EDIT, $resource,
                HyperLink::GET, 'advancedmarketplace/form/:id', ['id' => $resource->getId()]),
            MarketplaceAccessControl::MANAGE_PHOTO => $this->createHyperMediaLink(MarketplaceAccessControl::MANAGE_PHOTO, $resource,
                HyperLink::GET, 'advancedmarketplace-photo/form/:id', ['id' => $resource->getId()]),
            MarketplaceAccessControl::DELETE => $this->createHyperMediaLink(MarketplaceAccessControl::DELETE, $resource,
                HyperLink::DELETE, 'advancedmarketplace/:id', ['id' => $resource->getId()]),
        ]);

        if ($includeLinks) {
            $resource->setLinks([
                'likes' => $this->createHyperMediaLink(MarketplaceAccessControl::VIEW, $resource, HyperLink::GET, 'like', ['item_id' => $resource->getId(), 'item_type' => 'advancedmarketplace']),
                'comments' => $this->createHyperMediaLink(MarketplaceAccessControl::VIEW, $resource, HyperLink::GET, 'comment', ['item_id' => $resource->getId(), 'item_type' => 'advancedmarketplace'])
            ]);
        }
    }

    /**
     * @return AbstractResourceApi|\Apps\Core_MobileApi\Api\ResourceInterface|mixed
     * @throws \Exception
     */
    private function getTagApi()
    {
        return NameResource::instance()
            ->getApiServiceByResourceName(TagResource::RESOURCE_NAME);
    }

    public function getActions()
    {
        return [
            'advancedmarketplace/photos' => [
                'routeName' => 'formEdit',
                'params' => [
                    'module_name' => 'advancedmarketplace',
                    'resource_name' => 'advancedmarketplace',
                    'formType' => 'photos'
                ]
            ],
            'advancedmarketplace/invite' => [
                'routeName' => 'formEditItem',
                'params' => [
                    'data' => 'id',
                    'module_name' => 'advancedmarketplace',
                    'resource_name' => 'advancedmarketplace',
                    'formType' => 'invite'
                ]
            ],
            'advancedmarketplace/wishlist' => [
                'url' => 'mobile/advancedmarketplace/wishlist/:id',
                'method' => 'put',
                'data' => 'id=:id, is_wishlist=:is_wishlist',
            ],
        ];
    }

    public function searchFriendFilter($id, $friends)
    {
        $aInviteCache = $this->listingService->isAlreadyInvited($id, $friends);
        if (is_array($aInviteCache)) {
            foreach ($friends as $iKey => $friend) {
                if (isset($aInviteCache[$friend['user_id']])) {
                    $friends[$iKey]['is_active'] = $aInviteCache[$friend['user_id']];
                }
            }
        }
        return $friends;
    }

    public function getScreenSetting($param)
    {
        $screenSetting = new ScreenSetting('advancedmarketplace', []);
        $resourceName = MarketplaceResource::populate([])->getResourceName();
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_HOME);
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_LISTING);
        $screenSetting->addSetting($resourceName, ScreenSetting::MODULE_DETAIL, [
            ScreenSetting::LOCATION_HEADER => ['component' => 'item_header'],
            ScreenSetting::LOCATION_BOTTOM => ['component' => 'item_like_bar'],
            ScreenSetting::LOCATION_MAIN => [
                'component' => 'item_simple_detail',
                'embedComponents' => [
                    [
                        'component' => 'item_image',
                        'imageResizeMode' => 'contain',
                        'aspectRatio' => 1
                    ],
                    'item_title',
                    'item_pricing',
                    'item_author',
                    'item_stats',
                    'item_like_phrase',
                    [
                        'component' => 'item_pending',
                        'message' => 'listing_is_pending_approval'
                    ],
                    [
                        'component' => 'item_pending',
                        'message' => 'listing_expired_and_not_available_main_section',
                        'check_field' => 'is_expired'
                    ],
                    'item_category',
                    'item_location',
                    'item_description',
                    'item_html_content',
                    'item_tags',
                ]
            ]
        ]);
        return $screenSetting;
    }

    public function screenToController()
    {
        return [
            ScreenSetting::MODULE_HOME  => 'advancedmarketplace.index',
            ScreenSetting::MODULE_LISTING => 'advancedmarketplace.index',
            ScreenSetting::MODULE_DETAIL => 'advancedmarketplace.detail'
        ];
    }

    private function _getPaymentGateways()
    {
        $paymentGateways = $this->helperService->getPaymentGateways();
        $paymentGatewaysParsed = [];
        foreach ($paymentGateways as $paymentGateway) {
            $paymentGatewaysParsed[] = [
                'value' => $paymentGateway['gateway_id'],
                'label' => $paymentGateway['title']
            ];
        }
        return $paymentGatewaysParsed;
    }
}