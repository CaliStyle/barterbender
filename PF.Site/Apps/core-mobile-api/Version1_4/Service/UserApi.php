<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Version1_4\Service;

use Apps\Core_MobileApi\Adapter\Utility\UrlUtility;
use Apps\Core_MobileApi\Api\Resource\SubscriptionResource;
use Apps\Core_MobileApi\Api\Security\User\UserAccessControl;
use Apps\Core_MobileApi\Version1_4\Api\Form\User\UserRegisterForm;
use Exception;
use Phpfox;
use Phpfox_Request;
use User_Service_Process;

class UserApi extends \Apps\Core_MobileApi\Service\UserApi
{
    /**
     * @var User_Service_Process
     */
    private $processService;

    public function form($params = [])
    {
        $currentStep = $this->resolver->resolveSingle($params, 'current_step', 'int', [], 1);
        $nextStep = $this->resolver->resolveSingle($params, 'next_step', 'int', [], 2);
        $values = $this->resolver->resolveSingle($params, 'values', 'array', [], []);
        $this->denyAccessUnlessGranted(UserAccessControl::ADD);
        /** @var UserRegisterForm $form */
        $form = $this->createForm(UserRegisterForm::class, [
            'title'  => $this->getLocalization()->translate('sign_up', [
                'site' => $this->getSetting()->getAppSetting('core.site_title'),
            ]),
            'action' => UrlUtility::makeApiUrl('user'),
            'method' => 'post',
        ]);
        $form->setStep($currentStep);
        $form->setNextStep($nextStep);

        if ($currentStep > 1) {
            $form->assignValues($values);
            return $this->success([
                'module_name'   => 'user',
                'resource_name' => 'user',
                'formData'      => $form->getFormStructure()
            ]);
        }
        return $this->success($form->getFormStructure());
    }

    /**
     * Register user
     *
     * @param $params
     *
     * @return mixed
     * @throws Exception
     */
    public function create($params)
    {
        // by pass Anti-Spam Security Questions
        if (!defined('PHPFOX_IS_FB_USER')) {
            define('PHPFOX_IS_FB_USER', true);
        }
        $currentStep = $this->resolver->resolveSingle($params, 'current_step', 'int', [], 1);
        $this->denyAccessUnlessGranted(UserAccessControl::ADD);
        /** @var UserRegisterForm $form */
        $form = $this->createForm(UserRegisterForm::class);
        $form->setStep($currentStep);
        if ($form->isValid() && $values = $form->getValues()) {
            if ($form->isMultiStep() && $form->getStep() == 1) {
                $this->processCreate($values, true);
                //Process step 1
                return $this->form(['current_step' => 2, 'values' => $values]);
            } else {
                // force subscription
                $values['custom'] = $form->getGroupValues('custom');
                if (!empty($values['gender']) && $values['gender'] == '127') {
                    $values['gender'] = 'custom';
                }
                $id = $this->processCreate($values);

                //In case user must pay subscription
                $purchase = [];
                if (defined('PHPFOX_MUST_PAY_FIRST')) {
                    $purchase = Phpfox::getService('subscribe.purchase')->getPurchase(PHPFOX_MUST_PAY_FIRST, true);
                    $package = Phpfox::getService('subscribe')->getPackage($purchase['package_id']);
                    if ($package) {
                        $purchase['title'] = $package['title'];
                        $purchase['description'] = $package['description'];
                        $purchase['image_path'] = $package['image_path'];
                        $purchase['server_id'] = $package['server_id'];
                    }
                    $purchase = SubscriptionResource::populate($purchase)->toArray();
                    $id = $purchase['user_id'];
                }

                $user = Phpfox::getService('user')->get($id, true);
                if ($this->isPassed() && $user) {
                    return $this->success([
                        'id'               => (int)$user['user_id'],
                        'email'            => $user['email'],
                        'password'         => $values['password'],
                        'status_id'        => (int)$user['status_id'],
                        'pending_purchase' => $purchase
                    ], []);
                }
            }
        } else {
            return $this->validationParamsError($form->getInvalidFields());
        }

        return $this->error($this->getErrorMessage());

    }

    private function processCreate($values, $onlyValidate = false)
    {
        if (!empty($values['custom'])) {
            // Hard code to bypass custom fields checking
            Phpfox_Request::instance()->set('custom', $values['custom']);
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(':user')
            ->where("email = '" . $this->database()->escape($values['email']) . "'")
            ->execute('getSlaveField');
        if (!$iCnt) {
            if ($onlyValidate) {
                return true;
            }
            $id = $this->getProcessService()->add($values);
            return $id;
        } else {
            return $this->error($this->getLocalization()->translate('mobile_email_is_in_use_and_user_can_login', ['email' => $values['email']]));
        }
    }

    /**
     * @return User_Service_Process
     */
    private function getProcessService()
    {
        if (!$this->processService) {
            $this->processService = Phpfox::getService("user.process");
        }
        return $this->processService;
    }
}