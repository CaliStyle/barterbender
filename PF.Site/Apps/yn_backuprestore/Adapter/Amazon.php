<?php
/**
 * User: huydnt
 * Date: 06/01/2017
 * Time: 09:26
 */

namespace Apps\yn_backuprestore\Adapter;

use Aws\S3\S3Client;

class Amazon extends Abstracts
{
    private $s3;

    public function __construct($access_key, $secret_key)
    {
        $this->s3 = new S3Client([
            'region' => 'us-east-1',
            'version' => 'latest',
            'credentials' => [
                'key' => $access_key,
                'secret' => $secret_key,
            ],
        ]);
    }

    /**
     * upload file to bucket
     * @param $bucket_name
     * @param $file_path
     */
    public function upload($bucket_name, $file)
    {
        // upload file to bucket
        if (empty($name)) {
            $name = str_replace("\\", '/', str_replace(PHPFOX_DIR, '', $file));
        }
        $result = $this->s3->putObject([
            'Bucket' => $bucket_name,
            'Key' => $name,
            'SourceFile' => $file,
            'ACL' => 'public-read',
        ]);
    }

    /**
     * @return array|false|string
     */
    public function listBuckets($access_key, $secret_key)
    {
        try {
            $s3 = $this->s3 = new S3Client([
                'region' => 'us-east-1',
                'version' => 'latest',
                'credentials' => [
                    'key' => $access_key,
                    'secret' => $secret_key,
                ],
            ]);
            $bucketResults = $s3->listBuckets();
            if (is_array($bucketResults['Buckets'])) {
                $buckets = $bucketResults['Buckets'];
                // list buckets successfully
                if (!count($buckets)) {
                    return $buckets;
                } else {
                    foreach ($buckets as $bucket) {
                        $aAllBuckets[] = $bucket['Name'];
                    }
                    return $aAllBuckets;
                }
            } else {
                return $bucketResults;
            }
        } catch (\ErrorException $e) {
            return $e->getMessage();
        }
    }
}