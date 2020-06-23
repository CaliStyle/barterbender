<?php
/**
 * @author  phpFox LLC
 * @license phpfox.com
 */

namespace Apps\Core_MobileApi\Service;

use Core\Api\ApiServiceBase;

class ApiVersionResolver extends ApiServiceBase
{

    /**
     * @var String
     */
    private $inputVersion;

    /**
     * @var String
     */
    private $versionName;

    /**
     * @var array
     */
    private $availableVersions = [];


    const SUPPORT_API_VERSIONS = 'mobile, v1.4, v1.5, v1.6';

    /**
     * @return mixed
     */
    public function getVersionName()
    {
        return $this->versionName;
    }


    /**
     * @return array
     */
    public function getAvailableVersions()
    {
        return $this->availableVersions;
    }

    /**
     * @return String
     */
    public function getInputVersion()
    {
        return $this->inputVersion;
    }

    /**
     * @param $directory
     * @param $originClassName
     * @param $originalMethodName
     *
     * @return string
     */
    public function resolveClassNameVersion($directory, $originClassName, $originalMethodName)
    {
        $list = explode('\\', $originClassName);
        if ($list[0] === 'Apps') {
            $list[1] = $list[1] . '\\' . $directory;
        } else {
            $list[2] = $list[2] . '\\' . $directory;
        }
        $newClassName = implode('\\', $list);

        try {
            if (class_exists($newClassName)) {
                $reflectionClass = new \ReflectionClass($newClassName);
                $reflectionMethod = $reflectionClass->getMethod($originalMethodName);
                if (!empty($reflectionMethod)) {
                    return $newClassName;
                }
            }
        } catch (\Exception $exception) {

        }
        return null;
    }

    public function mapVersionToDirectory($version)
    {
        $list = explode('.', $version);
        $major = $list[0];
        $minor = count($list) > 1 ? $list[1] : '';
        return 'Version' . substr($major, 1) . ($minor ? '_' . $minor : '');
    }

    /**
     * Don\'t forget add class name to service mapping.
     *
     * @param $class
     * @param $method
     *
     * @return string
     */
    public function resolveServiceClassNameVersion($class, $method)
    {
        $resolveServiceClass = null;
        foreach ($this->availableVersions as $supportVersion => $directory) {
            if (null != ($newClassName = $this->resolveClassNameVersion($directory, $class, $method))) {
                return $newClassName;
            }
        }
        return null;
    }

    private function initVersion($params)
    {
        $supportVersions = array_reverse(array_map('trim', explode(',', static::SUPPORT_API_VERSIONS)));
        $versionName = isset($params['args']) ?
            isset($params['args']['api_version_name']) ? $params['args']['api_version_name'] : null : null;

        $versionName = $versionName ? $versionName : 'mobile';
        if ($versionName === 'mobile') {
            $versionName = 'v1';
        }

        $versionCheck = explode('.', $versionName, 3);
        $majorVersion = $versionCheck[0];
        $minorVersion = trim(count($versionCheck) > 1 ? $versionCheck[1] : '', '0');
        $patchVersion = trim(count($versionCheck) > 2 ? $versionCheck[2] : '', '0');

        $inputVersion = $majorVersion . ($minorVersion ? '.' . $minorVersion : '');

        foreach ($supportVersions as $supportVersion) {
            if (version_compare($supportVersion, $inputVersion, '<=')) {
                $this->availableVersions[$supportVersion] = $this->mapVersionToDirectory($supportVersion);
            }
        }

        if (!$params['args']) {
            $params['args'] = [];
        }

        $params['api_version_major'] = $majorVersion;
        $params['api_version_minor'] = $minorVersion;
        $params['api_version_patch'] = $patchVersion;

        $this->versionName = $versionName;
        $this->inputVersion = $inputVersion;
    }

    public function process($params, $transport, $method)
    {
        $this->initVersion($params);

        if (!headers_sent()) {
            header('Accept-Api-Version: ' . static::SUPPORT_API_VERSIONS);
        }
        $service = \Phpfox::getService($params['actual_api_service']);
        if (empty($params['maps'])) {
            $params['maps'] = [
                'get'    => 'get',
                'put'    => 'put',
                'post'   => 'post',
                'delete' => 'delete',
            ];
        }
        $method = isset($params['maps'][$method]) ? $params['maps'][$method] : $method;
        $resolveServiceClass = $this->resolveServiceClassNameVersion(get_class($service), $method);

        if ($resolveServiceClass) {
            $service = new $resolveServiceClass();
        }

        if (!method_exists($service, $method)) {
            return $this->error(_p('Method is\'t supported.'));
        }
        \Phpfox::getService('log.session')->setUserSession();
        return $service->process($params, $transport, $method);
    }
}