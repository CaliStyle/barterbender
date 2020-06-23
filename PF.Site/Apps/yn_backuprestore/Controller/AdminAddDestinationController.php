<?php

/**
 * User: huydnt
 * Date: 16/12/2016
 * Time: 12:03
 */
namespace Apps\yn_backuprestore\Controller;

use Admincp_Component_Controller_App_Index;
use Apps\yn_backuprestore\Adapter\Googledrive;
use Apps\yn_backuprestore\Adapter\Onedrive;
use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

class AdminAddDestinationController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $oTypeService = Phpfox::getService('ynbackuprestore.type');
        $oDestinationService = Phpfox::getService('ynbackuprestore.destination');
        $aTypes = $oTypeService->getAllTypes();
        $oRequest = $this->request();
        $aVal = $oRequest->getArray('val');
        $iId = $oRequest->get('req4', 0);
        $iTypeId = isset($aVal['type_id']) ? $aVal['type_id'] : 2;

        if ($iId && !count($aVal)) {
            $aDestination = $oDestinationService->getDestinationById($iId);
            $aParams = json_decode($aDestination['params'], true);
            unset($aDestination['params']);
            $aDestination = array_merge($aDestination, $aParams);
            $this->template()->assign([
                'aForms' => $aDestination,
            ]);
            $iTypeId = $aDestination['type_id'];
            if ($iTypeId == 3 && $aDestination['ftp_mode']) {
                $this->template()->assign('ftp_active', 'true');
            }
            if ($iTypeId == 4 && isset($aDestination['sftp_scp'])) {
                $this->template()->assign('sftp_scp', 'true');
            }
            if (isset($aDestination['s3_bucket']) && $aDestination['s3_bucket']) {
                $this->template()->assign('s3_bucket', $aDestination['s3_bucket']);
            }
        }

        if ($oRequest->get('submit_')) {
            /**
             * Validate form
             */
            $isValid = true;
            $errors = array();
            $valid_errors = array();
            if (!$aVal['title']) {
                $isValid = false;
                $errors[] = _p('Destination Name');
            }
            switch ($aVal['type_id']) {
                case 2: // Email
                    if (!$aVal['email_address']) {
                        $isValid = false;
                        $errors[] = _p('Email Address');
                        break;
                    }
                    $aEmails = explode(',', $aVal['email_address']);
                    array_walk($aEmails, function (&$sEmail) {
                        $sEmail = trim($sEmail);
                    });
                    $aValidEmails = array_filter($aEmails, function ($v){
                        return filter_var($v, FILTER_VALIDATE_EMAIL);
                    });
                    $aNotValidEmails = array_diff($aEmails, $aValidEmails);

                    if (!empty(array_diff($aEmails, $aValidEmails))) {
                        $isValid = false;
                        $valid_errors[] = _p('email_address_errors_are_not_valid_email', ['errors' => implode(', ', $aNotValidEmails)]);
                    }
                    $aVal['email_address'] = implode(',', $aEmails);
                    break;
                case 3: // FTP
                    if (!$aVal['ftp_server']) {
                        $isValid = false;
                        $errors[] = _p('FTP Server');
                    }
                    if (!$aVal['ftp_login']) {
                        $isValid = false;
                        $errors[] = _p('FTP Login');
                    }
                    if (!$aVal['ftp_password']) {
                        $isValid = false;
                        $errors[] = _p('FTP Password');
                    }
                    break;
                case 4: // SFTP
                    if (!$aVal['sftp_host']) {
                        $isValid = false;
                        $errors[] = _p('Host');
                    }
                    if (!$aVal['sftp_port']) {
                        $isValid = false;
                        $errors[] = _p('Port');
                    }
                    if (!$aVal['sftp_username']) {
                        $isValid = false;
                        $errors[] = _p('Username');
                    }
                    if (!$aVal['sftp_password']) {
                        $isValid = false;
                        $errors[] = _p('Password');
                    }
                    break;
                case 5: // MYSQL
                    if (!$aVal['mysql_host']) {
                        $isValid = false;
                        $errors[] = _p('Host');
                    }
                    if (!$aVal['mysql_dbname']) {
                        $isValid = false;
                        $errors[] = _p('Database Name');
                    }
                    if (!$aVal['mysql_username']) {
                        $isValid = false;
                        $errors[] = _p('Username');
                    }
                    break;
                case 6: // Amazon
                    if (!$aVal['s3_access']) {
                        $isValid = false;
                        $errors[] = _p('S3 Access Key');
                    }
                    if (!$aVal['s3_secret']) {
                        $isValid = false;
                        $errors[] = _p('S3 Secret Key');
                    }
                    if (!$aVal['s3_bucket'] || $aVal['s3_bucket'] == '...') {
                        $isValid = false;
                        $errors[] = _p('S3 Bucket');
                    }
                    break;
                case 7: // Dropbox
                    if (!$aVal['dropbox_key']) {
                        $isValid = false;
                        $errors[] = _p('App Key');
                    }
                    if (!$aVal['dropbox_secret']) {
                        $isValid = false;
                        $errors[] = _p('App Secret');
                    }
                    if (!$aVal['dropbox_token']) {
                        $isValid = false;
                        $errors[] = _p('Access Token');
                    }
                    break;
                case 8: // OneDrive
                    if (!$aVal['onedrive_id']) {
                        $isValid = false;
                        $errors[] = _p('Application ID');
                    }
                    if (!$aVal['onedrive_secret']) {
                        $isValid = false;
                        $errors[] = _p('Application Secret');
                    }
                    break;
                case 9: // GoogleD
                    if (!$aVal['google_id']) {
                        $isValid = false;
                        $errors[] = _p('Client ID');
                    }
                    if (!$aVal['google_secret']) {
                        $isValid = false;
                        $errors[] = _p('Client Secret');
                    }
                    break;
            }
            /**
             * Add destination if valid
             */
            if ($isValid) {
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['onedrive_callback'] = $_SESSION['google_callback'] = $this->url()->makeUrl('admincp.ynbackuprestore.destination');
                if ($iId) {
                    $_SESSION['google_destination_id'] = $iId;
                    $_SESSION['onedrive_destination_id'] = $iId;
                    $aDestination = $oDestinationService->getDestinationById($iId);
                    $aOldParams = json_decode($aDestination['params'], true);
                    if ($oDestinationService->updateDestination($iId, $aVal)) {
                        $newDestination = $oDestinationService->getDestinationById($iId);
                        // case Google Drive
                        if ($aVal['type_id'] == 9) {
                            $aParams = json_decode($newDestination['params'], true);
                            /**
                             * Check authorize of Google Drive destination
                             * - Case 1: destination not authorized yet
                             * - Case 2: destination authorized but:
                             *      Case 2.1: user change app ID and app Secret; each ID, secret have one access token
                             *      Case 2.2: refresh token cannot be used anymore
                             */
                            // Case 1
                            if (!isset($aParams['access_token'])) {
                                $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
                                    $this->url()->makeUrl('admincp.ynbackuprestore.get-google-access-token'));
                                $oGoogle->authorize();
                                exit();
                            } else {
                                // Case 2
                                $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
                                    $this->url()->makeUrl('admincp.ynbackuprestore.get-google-access-token'),
                                    $aParams['access_token']);

                                // Case 2.1
                                if ($aOldParams['google_id'] != $aParams['google_id'] || $aOldParams['google_secret'] != $aParams['google_secret']) {
                                    $oGoogle->authorize();
                                    exit();
                                } else {
                                    if ($oGoogle->isAccessTokenExpired()) {
                                        // Case 2.2
                                        if (!isset($aParams['access_token']['refresh_token'])) {
                                            $oGoogle->authorize();
                                            exit();
                                        } else {
                                            $aAccessToken = $oGoogle->refreshToken();
                                            if ($aAccessToken === false) {
                                                $oGoogle->authorize();
                                                exit();
                                            }
                                            // update access token
                                            $aParams['access_token'] = $aAccessToken;
                                            $oDestinationService->updateParams($iId, $aParams);
                                        }
                                    }
                                }
                            }
                        }

                        // case One Drive
                        if ($aVal['type_id'] == 8) {
                            $aParams = json_decode($newDestination['params'], true);
                            /**
                             * Redirect to authorize when
                             * - Case 1: destination not authorized yet
                             * - Case 2: destination authorized but:
                             *      Case 2.1: user change app ID and app Secret; each ID, secret have one access token
                             *      Case 2.2: refresh token cannot be used anymore
                             */
                            // Case 1
                            if (!isset($aParams['access_token'])) {
                                $oOneDrive = new Onedrive($aParams['onedrive_id']);
                                $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                                exit();
                            } else {
                                // Case 2.1
                                if ($aOldParams['onedrive_id'] != $aParams['onedrive_id'] || $aOldParams['onedrive_secret'] != $aParams['onedrive_secret']) {
                                    $oOneDrive = new Onedrive($aParams['onedrive_id']);
                                    $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                                    exit();
                                } else {
                                    // Case 2.2
                                    $oState = json_decode($newDestination['params'])->access_token;
                                    $oOneDrive = new Onedrive($aParams['onedrive_id'], $oState);
                                    if (isset($oState->token->data->refresh_token)) {
                                        $oOneDrive->renewAccessToken($aParams['onedrive_secret']);
                                        $access_token = $oOneDrive->getState();
                                        if (!isset($access_token->token->data->error)) {
                                            $aParams['access_token'] = $access_token;
                                            $oDestinationService->updateParams($iId, $aParams);
                                        } else {
                                            // if refresh fail
                                            $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                                            exit();
                                        }
                                    } else {
                                        $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                                        exit();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $iId = $oDestinationService->addDestination($aVal);

                    if ($iId) {
                        $aGoogleDest = Phpfox::getService('ynbackuprestore.destination')->getDestinationById($iId);
                        $aParams = json_decode($aGoogleDest['params'], true);

                        // Case Google
                        if ($aVal['type_id'] == 9) {
                            $_SESSION['google_destination_id'] = $iId;
                            $oGoogle = new Googledrive($aParams['google_id'], $aParams['google_secret'],
                                $this->url()->makeUrl('admincp.ynbackuprestore.get-google-access-token'));
                            $oGoogle->authorize();
                            exit();
                        }

                        // Case One Drive
                        if ($aVal['type_id'] == 8) {
                            $_SESSION['onedrive_destination_id'] = $iId;
                            $oOneDrive = new Onedrive($aParams['onedrive_id']);
                            $oOneDrive->authorize($this->url()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'));
                            exit();
                        }
                    }
                }
                $this->url()->send('admincp.ynbackuprestore.destination');
            } else {
                if (count($errors) || count($valid_errors)) {
                    $sErrors = '';
                    if (!empty(count($errors))) {
                        $sErrors = implode('</b>, <b>', $errors);
                        $sErrors = "<b>$sErrors</b>";
                        $sErrors = _p('{errors} are required.', ['errors' => $sErrors]);
                        $sErrors .= '<br />';
                    }
                    if (!empty($valid_errors)) {
                        $sErrors .= implode(', ', $valid_errors);
                    }
                    if (isset($aVal['sftp_scp'])) {
                        $this->template()->assign('sftp_scp', $aVal['sftp_scp']);
                    }
                    if (isset($aVal['ftp_mode']) && $aVal['ftp_mode']) {
                        $this->template()->assign('ftp_active', $aVal['ftp_mode']);
                    }
                    $this->template()->assign([
                        'sErrors' => $sErrors,
                        'aForms'  => $aVal
                    ]);
                }
            }
        }

        if ($iId) {
            $this->template()->assign('bIsEdit', true);
        }

        $this->template()
            ->setBreadCrumb(_p('Add New Destination'), $this->url()->makeUrl('admincp.ynbackuprestore.add-destination'))
            ->assign([
                'aTypes'               => $aTypes,
                'iTypeId'              => $iTypeId,
                'sOnedriveRedirectUri' => \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.get-onedrive-access-token'),
                'sGoogleRedirectUri'   => \Phpfox_Url::instance()->makeUrl('admincp.ynbackuprestore.get-google-access-token'),
                'sAssetsDir'           => Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/yn_backuprestore/assets/',
            ]);
    }
}