<?php

namespace Apps\Core_MobileApi\Service;


use Apps\Core_MobileApi\Adapter\MobileApp\MobileApp;
use Apps\Core_MobileApi\Adapter\MobileApp\MobileAppSettingInterface;
use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Apps\Core_MobileApi\Api\Form\Subscribe\CancelForm;
use Apps\Core_MobileApi\Api\Form\Subscribe\ChangePackageForm;
use Apps\Core_MobileApi\Api\Resource\SubscriptionResource;
use Apps\Core_MobileApi\Api\Security\AccessControl;
use Phpfox;

class SubscriptionApi extends AbstractResourceApi implements MobileAppSettingInterface
{

    protected $subscriptionService;
    protected $purchaseService;
    protected $purchaseProcessService;
    protected $processService;
    protected $reasonService;


    public function __naming()
    {
        return [
            'subscription/cancel'         => [
                'get'  => 'getCancelForm',
                'post' => 'cancelSubscription'
            ],
            'subscription/purchase/:id'   => [
                'get' => 'getPurchaseId'
            ],
            'subscription/checkout'       => [
                'post' => 'checkoutMembership'
            ],
            'subscription/change-package' => [
                'get'  => 'getChangePackageForm',
                'post' => 'changePackage'
            ],
        ];
    }

    public function __construct()
    {
        parent::__construct();
        $this->subscriptionService = Phpfox::getService('subscribe');
        $this->purchaseService = Phpfox::getService('subscribe.purchase');
        $this->purchaseProcessService = Phpfox::getService('subscribe.purchase.process');
        $this->processService = Phpfox::getService('subscribe.process');
        $this->reasonService = Phpfox::getService('subscribe.reason');
    }

    function findAll($params = [])
    {
        // TODO: Implement findAll() method.
    }

    function findOne($params)
    {
        // TODO: Implement findOne() method.
    }

    function create($params)
    {
        $this->denyAccessUnlessGranted(AccessControl::IS_AUTHENTICATED);
    }

    public function processCreate($values)
    {
        if (empty($values['package_id'])) {
            return false;
        }
        $id = $this->getUser()->getId();
        $package = $this->subscriptionService->getPackage($values['package_id']);
        if (isset($package['package_id'])) {
            if (Phpfox::getUserBy('user_group_id') == $package['user_group_id']) {
                return $this->error($this->getLocalization()->translate('attempting_to_upgrade_to_the_same_user_group_you_are_already_in'));
            }
            $package['default_currency_id'] = isset($package['default_currency_id']) ? $package['default_currency_id'] : $package['price'][0]['alternative_currency_id'];
            $package['default_cost'] = isset($package['default_cost']) ? $package['default_cost'] : $package['price'][0]['alternative_cost'];

            $purchaseId = $this->purchaseProcessService->add([
                'package_id'  => $package['package_id'],
                'currency_id' => $package['default_currency_id'],
                'price'       => $package['default_cost']
            ], $id
            );

            $defaultCost = (int)str_replace('.', '', $package['default_cost']);

            if ($purchaseId) {
                if ($defaultCost > 0) {
                    define('PHPFOX_MUST_PAY_FIRST', $purchaseId);

                    $this->purchaseProcessService->changePurchaseForSigningUp($purchaseId, $id);

                    return $purchaseId;
                } else {
                    Phpfox::getService('subscribe.purchase.process')->update($purchaseId, $package['package_id'], 'completed', $id, $package['user_group_id'], $package['fail_user_group']);

                    return true;
                }
            }
        }
        return false;
    }

    function update($params)
    {
        return null;
    }

    function patchUpdate($params)
    {
        return null;
    }

    function delete($params)
    {
        return null;
    }

    function form($params = [])
    {
        return null;
    }

    function approve($params)
    {
        return null;
    }

    function feature($params)
    {
        return null;
    }

    function sponsor($params)
    {
        return null;
    }

    function loadResourceById($id, $returnResource = false)
    {
        return null;
    }

    public function getAppSetting($param)
    {
        $l = $this->getLocalization();
        return new MobileApp('subscribe', [
            'title'           => $l->translate('members'),
            'home_view'       => 'tab',
            'main_resource'   => new SubscriptionResource([]),
            'other_resources' => []
        ]);
    }

    public function getCancelForm($params)
    {
        $this->denyAccessUnlessGranted(AccessControl::IS_AUTHENTICATED);
        $purchaseId = $this->resolver->setRequired(['purchase_id'])->resolveSingle($params, 'purchase_id');
        /** @var CancelForm $form */
        $form = $this->createForm(CancelForm::class, [
            'title'  => $this->getLocalization()->translate('cancel_subscription'),
            'action' => UrlUtility::makeApiUrl('subscription/cancel'),
            'method' => 'post',
        ]);
        $form->setPurchaseId($purchaseId);
        return $this->success($form->getFormStructure());
    }

    public function cancelSubscription($params)
    {
        $this->denyAccessUnlessGranted(AccessControl::IS_AUTHENTICATED);
        $form = $this->createForm(CancelForm::class);
        if ($form->isValid() && $values = $form->getValues()) {
            if ($this->processCancelSubscription($values)) {
                return $this->success([], [], $this->getLocalization()->translate('successfully_your_membership_has_been_changed_please_you_must_login_again'));
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
        return $this->error();
    }

    private function processCancelSubscription($values)
    {
        $purchase = $this->purchaseService->getPurchase($values['purchase_id'], true);
        if (empty($purchase) || $purchase['status'] != 'completed') {
            return $this->notFoundError();
        }
        $this->reasonService->cancelSubscriptionByUser($values['purchase_id'], $purchase['fail_user_group'], $this->getUser()->getId(), $purchase['package_id'], $values['reason']);
        return true;
    }

    public function loadPurchaseById($id, $detail = false)
    {
        $purchase = $this->purchaseService->getPurchase($id, $detail);
        if (empty($purchase['purchase_id'])) {
            return null;
        }
        if ($detail) {
            $package = $this->subscriptionService->getPackage($purchase['package_id']);
            if ($package) {
                $purchase['title'] = $package['title'];
                $purchase['description'] = $package['description'];
                $purchase['image_path'] = $package['image_path'];
                $purchase['server_id'] = $package['server_id'];
            }
        }

        /** @var SubscriptionResource $purchase */
        $purchase = SubscriptionResource::populate($purchase);
        return $purchase->toArray();
    }

    public function getPurchaseId($params)
    {
        $id = $this->resolver->resolveId($params);
        $result = $this->loadPurchaseById($id, true);
        if (!$result) {
            return $this->notFoundError();
        }
        return $this->success($result);
    }

    public function getMembershipDetail($packageId)
    {
        $userId = $this->getUser()->getId();
        if (!$userId) {
            return false;
        }
        $purchase = $this->database()->select('purchase_id')->from(':subscribe_purchase')
            ->where([
                'user_id'    => $userId,
                'status'     => 'completed',
                'package_id' => $packageId
            ])->execute('getField');
        if (!$purchase) {
            return false;
        }
        $invoice = Phpfox::getService('subscribe.purchase')->getInvoice($purchase);
        $result = [];
        if ($invoice) {
            $result = SubscriptionResource::populate($invoice)->toArray();
        }
        return $result;
    }

    public function checkoutMembership($params)
    {
        $params = $this->resolver
            ->setRequired(['price', 'currency', 'gateway_id', 'item_number'])
            ->resolve($params)->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->missingParamsError($this->resolver->getInvalidParameters());
        }
        if ($params['gateway_id'] == 'activitypoints' && Phpfox::isAppActive('Core_Activity_Points') && Phpfox::getUserParam('activitypoint.can_purchase_with_activity_points')) {
            $aParts = explode('|', $params['item_number']);
            if ($aReturn = Phpfox::getService('activitypoint.process')->purchaseWithPoints($aParts[0], $aParts[1],
                $params['price'], $params['currency'])
            ) {
                return $this->success([
                    'restart_app' => true,
                ], [], $this->getLocalization()->translate('successfully_your_membership_has_been_changed_please_you_must_login_again'));
            }
        } else {

        }
        return $this->permissionError();
    }

    public function getActions()
    {
        return [
            'subscription/change-package' => [
                'routeName' => 'formEdit',
                'params'    => [
                    'module_name'   => 'subscribe',
                    'resource_name' => 'subscription',
                    'formType'      => 'changePackage',
                ]
            ]
        ];
    }

    public function getChangePackageForm($params)
    {
        /** @var CancelForm $form */
        $params = $this->resolver->setRequired(['user_id'])
            ->setDefined(['package_id'])
            ->setAllowedTypes('package_id', 'int')
            ->setAllowedTypes('user_id', 'int')
            ->resolve($params)->getParameters();
        $userId = $params['user_id'];
        if (!$userId) {
            return $this->missingParamsError($this->resolver->getInvalidParameters());
        }
        /** @var ChangePackageForm $form */
        $form = $this->createForm(ChangePackageForm::class, [
            'title'  => $this->getLocalization()->translate('membership'),
            'action' => UrlUtility::makeApiUrl('subscription/change-package'),
            'method' => 'post',
        ]);
        $form->assignValues([
            'package_id' => (int)$params['package_id'],
            'user_id'    => (int)$params['user_id']
        ]);
        return $this->success($form->getFormStructure());
    }

    public function changePackage()
    {
        $form = $this->createForm(ChangePackageForm::class);
        if ($form->isValid() && $values = $form->getValues()) {
            if ($id = $this->processChangePackage($values)) {
                $purchase = $this->loadPurchaseById($id, true);
                $purchase['extra_action'] = [
                    'label'  => $this->getLocalization()->translate('change_membership_package'),
                    'action' => 'subscription/change-package',
                    'params' => [
                        'module_name' => 'subscription'
                    ]
                ];
                return $this->success([
                    'pending_purchase' => $purchase,
                    'restart_app'      => true,
                ], []);
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }
        return $this->error();
    }

    protected function processChangePackage($values)
    {
        $package = $this->subscriptionService->getPackage($values['package_id']);
        if (!$package) {
            return $this->notFoundError();
        }
        $package['default_currency_id'] = isset($package['default_currency_id']) ? $package['default_currency_id'] : $package['price'][0]['alternative_currency_id'];
        $package['default_cost'] = isset($package['default_cost']) ? $package['default_cost'] : $package['price'][0]['alternative_cost'];

        $purchaseId = $this->purchaseProcessService->add([
            'package_id'  => $package['package_id'],
            'currency_id' => $package['default_currency_id'],
            'price'       => $package['default_cost'],
            'renew_type'  => $package['recurring_period'] > 0 ? 1 : 0, //auto renew
        ], $values['user_id']
        );
        if ($package['default_cost'] == '0.00') {
            $this->purchaseProcessService->update($purchaseId, $package['package_id'], 'completed', $values['user_id'], $package['user_group_id'], false);
            if ((int)$package['is_recurring'] != 0) {
                $this->purchaseProcessService->updatePurchaseForFirstTimeForFreeAndRecurring($purchaseId);
            }
        }
        $this->purchaseProcessService->changePurchaseForSigningUp($purchaseId, $values['user_id']);

        return $purchaseId;
    }
}