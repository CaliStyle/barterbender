<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/24/17
 * Time: 11:19
 */
defined('PHPFOX') or exit('NO DICE!');

require_once dirname(dirname(__file__)) . '/service/paymentgateway.class.php';
require_once dirname(dirname(__file__)) . '/service/Stripe/Stripe.php';

class Ynadvancedpayment_Service_Stripe extends Ynadvancedpayment_Service_Paymentgateway
{
    protected $_test_mode = '';
    protected $_gateway_id = '';
    protected $_api_key = '';
    protected $_username = '';

    public function initialize($gateway, $params)
    {
        $this->_order = $params;
        $this->_gatewaySettings = (array) $gateway['config'];
        $this->_gatewaySettings['test_mode'] = $gateway['test_mode'];
        $this->_secret_key = $this->plugin_settings('stripe_secret_key');
        $this->_public_key = $this->plugin_settings('stripe_public_key');
        if ($this->plugin_settings('test_mode'))
        {
            $this->_test_mode = "TRUE";
        }
        else
        {
            $this->_test_mode = "FALSE";
        }
    }
    public function process_payment($gateway, $params,$package = null)
    {
        $errors = [];
        $resp = array();

        if (isset($_POST['stripeToken']))
        {
            $token = $_POST['stripeToken'];

            // Check for a duplicate submission, just in case:
            // Uses sessions, you could use a cookie instead.
            if (isset($_SESSION['token']) && ($_SESSION['token'] == $token))
            {
                $errors['token'] = 'You have apparently resubmitted the form. Please do not do that.';
                return Phpfox_Error::set($errors['token']);
            }
            else
            {
                // New submission.
                $_SESSION['token'] = $token;
            }
        }
        else
        {
            $errors['token'] = 'The order cannot be processed. Please make sure you have JavaScript enabled and try again.';
            return Phpfox_Error::set($errors['token']);
        }

        $this->initialize($gateway, $params);
        $amount = ($this->order('amount')) * 100;
        // create the charge on Stripe's servers - this will charge the user's card
        try
        {
            // set your secret key: remember to change this to your live secret key in production
            // see your keys here https://manage.stripe.com/account
            $Stripe = new Ynadvancedpayment_Service_Stripe_Stripe();
            $Stripe -> includeFiles();
            Stripe::setApiKey($this->_secret_key);
            $order_id = explode('|', $this->_order['item_number']);
            if($package != null){
                $package_id = $this->getPackageId($order_id[1]);
                $aUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
                $customer = Stripe_Customer::create(array("card" => $token, "plan" => $package_id, "email" => $aUser['email'], "metadata" => array('order_id' => $order_id[1]), ));
                $customer_info = Stripe_Customer::retrieve($customer -> id);
                $order_id = $customer_info -> metadata['order_id'];
                return $order_id;
            }
            else{
                // Charge the order:
                $charge = Stripe_Charge::create(array(
                    "amount" => $amount, // amount in cents, again
                    "currency" => $this->_order['currency_code'],
                    "card" => $token,
                    "metadata" => array('order_id' => $this -> _order['item_number']),
                ));
            }

            // Check that it was paid:
            if ($charge -> paid == true)
            {

                // Store the order in the database.
                // Send the email.
                // Celebrate!
                $resp['authorized'] = TRUE;
                $resp['transaction_id'] = $charge -> id;
                $resp['amount'] = $charge -> amount/100;
                $resp['currency'] = strtoupper($charge -> currency);

                return $resp;

            }
            else
            {
                // Charge was not paid!
                $resp['failed'] = 1;
                $resp['error_message'] = 'not charged';
                return $resp;
            }

        }
        catch (Stripe_CardError $e)
        {
            // Card was declined.
            $e_json = $e -> getJsonBody();
            $err = $e_json['error'];
            $errors['stripe'] = $err['message'];
            return Phpfox_Error::set($errors['stripe']);
        }
        catch (Stripe_ApiConnectionError $e)
        {
            // Network problem, perhaps try again.
            return Phpfox_Error::set(_p('Network problem, perhaps try again'));
        }
        catch (Stripe_InvalidRequestError $e)
        {
            // You screwed up in your programming. Shouldn't happen!
            return Phpfox_Error::set(_p('You screwed up in your programming. Should not happen!'));
        }
        catch (Stripe_ApiError $e)
        {
            // Stripe's servers are down!
            return Phpfox_Error::set(_p('Stripe servers are down!'));
        }
        catch (Stripe_CardError $e)
        {
            // Something else that's not the customer's fault.
            return Phpfox_Error::set(_p('Something else that is not the customer fault.'));
        }
    }
    public function setApiPublicKey($sKey)
    {
        $Stripe = new Ynadvancedpayment_Service_Stripe_Stripe();
        $Stripe -> includeFiles();
        Stripe::setApiKey($sKey);
    }
}