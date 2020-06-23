<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 17:40
 */

namespace Apps\yn_backuprestore\Adapter;

require_once 'libs/vendor/autoload.php';
use phpseclib\Net\SCP;
use phpseclib\Net\SFTP;
use phpseclib\Net\SSH2;

class SftpServer extends Abstracts
{
    protected $Sftp;
    protected $Scp;

    /**
     * Connect to SFTP Server
     * @param $server
     * @param $port
     * @param $username
     * @param $passwd
     * @param bool $useSCP
     * @return bool
     */
    public function connect($server, $port, $username, $passwd, $useSCP = false)
    {
        if ($useSCP) {
            $ssh2 = new SSH2($server, $port);
            if ($ssh2->login($username, $passwd)) {
                $this->Scp = new SCP($ssh2);
            } else {
                return false;
            }
        }
        $this->Sftp = new SFTP($server, $port);
        if ($this->Sftp->login($username, $passwd)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Upload file to SFTP Server
     * @param $server_dir
     * @param $file_path
     * @return bool
     */
    public function upload($server_dir, $file_path)
    {
        $aDirs    = explode(DIRECTORY_SEPARATOR, $server_dir);
        $aDirs    = array_filter($aDirs);
        $sRemoteDir     = PHPFOX_DS . implode(PHPFOX_DS, $aDirs) . PHPFOX_DS;
        $bIsFirst = true;
        foreach ($aDirs as $sDir) {
            if (!$sDir) {
                continue;
            }
            if ($bIsFirst) {
                $sDir     = DIRECTORY_SEPARATOR . $sDir;
                $bIsFirst = false;
            }
            if (!$this->Sftp->chdir($sDir)) {
                $this->Sftp->mkdir($sDir);
                $this->Sftp->chdir($sDir);
            }
        }

        if (isset($this->Scp)) {
            if (!$this->Scp->put($sRemoteDir . basename($file_path), $file_path, 1)) {
                return false;
            }
        } else {
            if (!$this->Sftp->put(basename($file_path), $file_path, 1)) {
                return false;
            }
        }

        return true;
    }
}