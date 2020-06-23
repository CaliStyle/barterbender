<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Service;

use Apps\Core_MobileApi\Api\AbstractResourceApi;
use Aws\S3\S3Client;
use Phpfox;
use Phpfox_File;
use Phpfox_Plugin;
use Phpfox_Request;

class FileApi extends AbstractResourceApi
{
    private $tempFileService;

    public function __construct()
    {
        parent::__construct();
        $this->tempFileService = Phpfox::getService('core.temp-file');
    }

    public function __naming()
    {
        return [
            'file/upload-video' => [
                'post' => 'uploadVideo',
            ],
        ];
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
        $params = $this->resolver->setDefined([
            'name',
            'item_type',
            'is_temp',
            'max_size',
            'no_square',
            'allow_type',
            'upload_dir',
            'sub_dir',
            'update_space',
            'thumbnail_sizes'
        ])
            ->resolve($params)
            ->setAllowedTypes('max_size', 'int')
            ->setRequired([
                'name',
                'item_type'
            ])
            ->setDefault([
                'upload_dir' => 'core.dir_pic',
                'is_temp'    => 1
            ])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getInvalidParameters());
        }
        $file = $this->resolver->getFile($params['name']);
        $type = $params['item_type'];
        $imageSize = getimagesize($file);
        $l = $this->getLocalization();

        if (empty($file) || is_array($file)) {
            return $this->notFoundError($l->translate('upload_fail_please_try_again_later'));
        }
        $fileName = preg_replace('/&#/i', 'u', preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName()));
        if (empty($params['upload_dir'])) {
            return $this->missingParamsError(['upload_dir']);
        }

        if (!empty($params['update_space']) && !$this->database()->isField(':user_space', 'space_' . $params['item_type'])) {
            return $this->notFoundError($l->translate('upload_failed_cannot_update_user_space_for_type_type', ['type' => $params['item_type']]));
        }

        if (empty($params['is_temp'])) {
            $uploadDir = Phpfox::getParam($params['upload_dir']) !== null
                ? rtrim(Phpfox::getParam($params['upload_dir']),
                    '/') . (!empty($params['sub_dir']) ? '/' . $params['sub_dir'] : '') . '/'
                : $params['upload_dir'];
            $allowType = [];
            if (!empty($params['allow_type'])) {
                if (!is_array($params['allow_type'])) {
                    $allowType = explode(',', $params['allow_type']);
                    $allowType = array_map(function ($type) {
                        return trim($type);
                    }, $allowType);
                } else {
                    $allowType = $params['allow_type'];
                }
            }
            $thumbnail = [];
            if (!empty($params['thumbnail_sizes'])) {
                if (!is_array($params['thumbnail_sizes'])) {
                    $thumbnail = explode(',', $params['thumbnail_sizes']);
                    $thumbnail = array_map(function ($thumb) {
                        if (is_numeric($thumb) && $thumb > 0) {
                            return $thumb;
                        }
                        return null;
                    }, $thumbnail);
                } else {
                    $thumbnail = $params['thumbnail_sizes'];
                }
            }
            $callback = [
                'type'            => $params['item_type'],
                'upload_dir'      => $uploadDir,
                'update_space'    => isset($params['update_space']) ? $params['update_space'] : false,
                'modify_name'     => true,
                'no_square'       => isset($params['no_square']) ? $params['no_square'] : false,
                'type_list'       => $allowType,
                'max_size'        => $params['max_size'] > 0 ? $params['max_size'] : null,
                'thumbnail_sizes' => $thumbnail
            ];
        } else {
            //If upload direct to folder not using temp file
            if (!Phpfox::hasCallback($type, 'getUploadParams')) {
                return $this->notFoundError($l->translate('missing_callback_type_getuploadparams', ['type' => $type]));
            }
            $callback = Phpfox::callback($type . '.getUploadParams');
            $callback['type'] = $type;
        }
        //Suppot some special cases
        switch ($type) {
            case 'photo':
                if (!isset($callback['thumbnail_sizes'])) {
                    $callback['thumbnail_sizes'] = Phpfox::getService('photo')->getPhotoPicSizes();
                }
                $callback['modify_name'] = true;
                $callback['no_square'] = true;
                break;
            case 'music_song':
                $callback['type'] = 'music';
                break;
            case 'comment_comment':
                $callback['update_space'] = false;
                break;
            default:
                break;
        }

        if ($sPlugin = Phpfox_Plugin::get('mobile.service_fileapi_create_callback_upload')) {
            eval($sPlugin);
        }
        if ($type == 'attachment') {
            $callback['upload_dir'] = Phpfox::getParam('core.dir_attachment');
        }
        if (empty($callback['update_space'])) {
            $callback['update_space'] = false;
        }
        $uploadedFile = Phpfox::getService('user.file')->upload($params['name'], $callback);
        if (!$uploadedFile) {
            return $this->error(_p('upload_fail_please_try_again_later'));
        }
        if (!empty($uploadedFile['error'])) {
            return $this->error($uploadedFile['error']);
        }
        $fileId = phpFox::getService('core.temp-file')->add([
            'type'      => $type,
            'size'      => $uploadedFile['size'],
            'path'      => $uploadedFile['name'],
            'server_id' => Phpfox_Request::instance()->getServer('PHPFOX_SERVER_ID')
        ]);
        $fileExtra = [
            'name' => $fileName,
            'size' => $file->getClientSize(),
            'ext'  => $file->getClientOriginalExtension(),
            'type' => $file->getClientMimeType()
        ];
        if (!empty($imageSize)) {
            $fileExtra['width'] = $imageSize[0];
            $fileExtra['height'] = $imageSize[1];
        }
        //Update extra info to temp file
        $this->database()->update(':temp_file', ['extra_info' => json_encode($fileExtra)], 'file_id = ' . (int)$fileId);

        return $this->success([
            'temp_file'  => $fileId,
            'file_extra' => $fileExtra
        ]);
    }

    public function uploadVideo($params)
    {
        $params = $this->resolver->setDefined([
            'name',
            'item_type',
        ])
            ->resolve($params)
            ->setRequired([
                'name',
                'item_type'
            ])
            ->setDefault([
                'item_type' => 'v'
            ])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getInvalidParameters());
        }
        $file = $this->resolver->getFile($params['name']);
        if (empty($file)) {
            return $this->notFoundError($this->getLocalization()->translate('upload_fail_please_try_again_later'));
        }
        $type = $params['item_type'];
        if (!Phpfox::hasCallback($type, 'getUploadParams')) {
            return $this->notFoundError($this->getLocalization()->translate('missing_callback_type_getuploadparams', ['type' => $type]));
        }
        $callback = Phpfox::callback($type . '.getUploadParams');
        if (empty($callback['upload_dir'])) {
            $callback['upload_dir'] = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS;
        }
        if (empty($callback['update_space'])) {
            $callback['update_space'] = false;
        }
        $callback['type'] = $type;
        $userId = $this->getUser()->getId();
        $ext = $file->getClientOriginalExtension();
        if ($loadedFile = Phpfox::getService('user.file')->load($params['name'], $callback)) {
            //Process video
            $sId = md5(uniqid() . $userId);
            $realName = $sId . '.' . $ext;
            $path = $callback['upload_dir'];
            $date = date('y/m/d/');
            $name = $date . $realName;
            if (!@move_uploaded_file($_FILES[$params['name']]['tmp_name'], $path . $realName)) {
                return $this->error();
            }

            $iMethodUpload = $this->getSetting()->getAppSetting('v.pf_video_method_upload');
            if ($iMethodUpload == 1 && setting('pf_video_key') && setting('pf_video_s3_key')) {
                $bucket = setting('pf_video_s3_bucket');
                $region = setting('pf_video_s3_region', 'us-east-2');
                $_oS3Client = new S3Client([
                    'region'      => $region,
                    'version'     => 'latest',
                    'credentials' => [
                        'key'    => setting('pf_video_s3_key'),
                        'secret' => setting('pf_video_s3_secret'),
                    ],
                ]);

                $_oS3Client->putObject([
                    'Bucket'     => $bucket,
                    'Key'        => $name,
                    'SourceFile' => $path . $realName,
                    'ACL'        => 'public-read',
                ]);
                try {
                    $zencoder = new \Services_Zencoder(setting('pf_video_key'));
                    $params = [
                        "input"         => 's3://' . $bucket . '/' . $name,
                        'notifications' => [
                            'url' => url('/video/callback')
                        ],
                        "outputs"       => [
                            [
                                "label"        => "mp4 high",
                                'h264_profile' => 'high',
                                'url'          => 's3://' . $bucket . '/' . $date . $sId . '.mp4',
                                'public'       => true,
                                'thumbnails'   => [
                                    'label'    => 'thumb',
                                    'size'     => '852x480',
                                    'base_url' => 's3://' . $bucket . '/' . $date . $sId . '.png',
                                    'number'   => 3
                                ]
                            ]
                        ]
                    ];

                    $encoding_job = $zencoder->jobs->create($params);

                    storage()->set('pf_video_' . $encoding_job->id, [
                        'encoding_id'   => $encoding_job->id,
                        'video_path'    => $date . $sId . '.mp4',
                        'user_id'       => Phpfox::getUserId(),
                        'id'            => $sId,
                        'ext'           => $ext,
                        'default_image' => $date . $sId . '.png/frame_0001.png'
                    ]);

                    $pf_video_id = $encoding_job->id;

                } catch (\Services_Zencoder_Exception $e) {
                    return $this->error($e->getMessage());
                }
            } else if ($iMethodUpload == 0 && $this->getSetting()->getAppSetting('v.pf_video_ffmpeg_path')) {

                storage()->set('pf_video_' . $sId, [
                    'path'    => $path . $realName,
                    'user_id' => $userId,
                    'id'      => $sId,
                    'ext'     => Phpfox_File::instance()->getFileExt($_FILES['ajax_upload']['name'])
                ]);

                $pf_video_id = $sId;
            } else {
                return $this->error();
            }
            $fileExtra = [
                'name' => preg_replace('/&#/i', 'u', preg_replace('/\\.[^.\\s]{3,4}$/', '', $file->getClientOriginalName())),
                'size' => $file->getClientSize(),
                'ext'  => $ext,
                'type' => $file->getClientMimeType()
            ];
            return $this->success([
                'temp_file'  => $pf_video_id,
                'file_extra' => $fileExtra
            ]);
        } else {
            return $this->error($loadedFile['error']);
        }
    }

    function update($params)
    {
        // TODO: Implement update() method.
    }

    function patchUpdate($params)
    {
        // TODO: Implement updateAll() method.
    }

    function delete($params)
    {
        $params = $this->resolver
            ->setDefined([
                'delete_source'
            ])
            ->setAllowedValues('delete_source', ['0', '1'])
            ->setAllowedTypes('category', 'int')
            ->setRequired(['id'])
            ->setDefined([
                'delete_source' => 0
            ])
            ->resolve($params)
            ->getParameters();
        if (!$this->resolver->isValid()) {
            return $this->validationParamsError($this->resolver->getInvalidParameters());
        }
        $item = $this->tempFileService->get($params['id']);
        if (empty($item)) {
            return $this->notFoundError();
        }
        if ($item['user_id'] == $this->getUser()->getId() && $this->tempFileService->delete($params['id'], $params['delete_source'])) {
            return $this->success([], [], $this->getLocalization()->translate('file_deleted_successfully'));
        }
        return $this->permissionError();
    }

    function form($params = [])
    {
        // TODO: Implement form() method.
    }

    function loadResourceById($id, $returnResource = false)
    {
        // TODO: Implement loadResourceById() method.
    }

    function approve($params)
    {
        // TODO: Implement approve() method.
    }

    function feature($params)
    {
        // TODO: Implement feature() method.
    }

    function sponsor($params)
    {
        // TODO: Implement sponsor() method.
    }
}