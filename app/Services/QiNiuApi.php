<?php
namespace App\Services;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;


/**
 * - 七牛云Api
 * Created by PhpStorm.
 * User: xiexiang
 * Date: 2018/7/23
 * Time: 下午1:54
 */
class QiNiuApi {

    private $accessKey; //私钥AK
    private $secretKey; //私钥SK
    private $bucket;    //空间名（对象存储中）
    public $baseUrl;   //外链默认域名（对象存储->内容管理）
    private $picPrefix; //文件前缀

    /**
     * QiNiuApi constructor.
     * @param $accessKey  //私钥AK
     * @param $secretKey  //私钥SK
     * @param $bucket     //空间名（对象存储中）
     * @param $baseUrl    //外链默认域名（对象存储->内容管理）
     * @param $picPrefix  //文件前缀
     */
    public function __construct($accessKey, $secretKey, $bucket, $baseUrl, $picPrefix )
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->bucket    = $bucket;
        $this->baseUrl   = $baseUrl;
        $this->picPrefix = $picPrefix;
    }

    /**
     * - 上传
     * @param $fileName
     * @param $filePath
     * @return mixed
     * @throws \Exception
     */
    public function upload($fileName, $filePath)
    {
        $upManager = new UploadManager();
        $auth = new Auth($this->accessKey, $this->secretKey);
        $token = $auth->uploadToken($this->bucket);
        $key =  $this->picPrefix.md5($fileName.rand(100, 999));
        list($ret, $error) = $upManager->putFile($token, $key, $filePath);
        if(!empty($ret)){
            return $this->baseUrl.'/'.$ret['key'];
        }
        return $error;
    }
}