<?php
/**
 * User: huydnt
 * Date: 05/01/2017
 * Time: 17:06
 */

namespace Apps\yn_backuprestore\Adapter;


class FtpServer extends Abstracts
{
    protected $ftp_conn;

    /**
     * Connect to FTP Server
     * @param $ftp_server
     * @param $ftp_username
     * @param $ftp_password
     * @param $passive_mode
     * @return bool
     */
    public function connect($ftp_server, $ftp_username, $ftp_password, $passive_mode)
    {
        $this->ftp_conn = ftp_connect($ftp_server);
        if ($this->ftp_conn === false) {
            return false;
        }
        if (!@ftp_login($this->ftp_conn, $ftp_username, $ftp_password)) {
            return false;
        }
        if ($passive_mode) {
            if (!ftp_pasv($this->ftp_conn, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Upload file to FTP Server
     * @param $file_path String, Path of the file
     * @param $location String, Location to store file
     * @return bool
     */
    public function upload($file_path, $location)
    {
        $filename = basename($file_path);
        if ($location) {
            str_replace('/', DIRECTORY_SEPARATOR, $location);
            $dirs = explode(DIRECTORY_SEPARATOR, $location);
            foreach ($dirs as $dir) {
                if (!$dir) {
                    continue;
                }
                if (!$this->ftp_is_dir($this->ftp_conn, $dir)) {
                    ftp_mkdir($this->ftp_conn, $dir);
                    ftp_chdir($this->ftp_conn, $dir);
                } else {
                    ftp_chdir($this->ftp_conn, $dir);
                }
            }
        }
        $result = ftp_put($this->ftp_conn, $filename, $file_path, FTP_ASCII);

        return $result;
    }

    /**
     * Check if directory existed
     * @param $ftp
     * @param $dir
     * @return bool
     */
    protected function ftp_is_dir($ftp, $dir)
    {
        $pushd = ftp_pwd($ftp);

        if ($pushd !== false && @ftp_chdir($ftp, $dir)) {
            ftp_chdir($ftp, $pushd);
            return true;
        }

        return false;
    }

    /**
     * Close the connection
     * @return bool
     */
    public function close()
    {
        return ftp_close($this->ftp_conn);
    }
}