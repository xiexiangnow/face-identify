<?php
namespace App\Services;
use HttpClient;


/**
 * Created by PhpStorm.
 * User: xiexiang
 * Date: 2018/7/23
 * Time: 上午10:49
 */
class ThirdFaceApi {

    private $faceApiKey;    //调用此API的API Key
    private $faceApiSecret; //调用此API的API Secret

    public function __construct($faceApiKey, $faceApiSecret)
    {
        $this->faceApiKey    = $faceApiKey;
        $this->faceApiSecret = $faceApiSecret;
    }

    /**
     * - 获取token
     * @param $imageUrl  //图片的 URL
     * @return string
     * @throws \CException
     */
    public function getFaceToken($imageUrl)
    {
        $re = HttpClient::from()->post("https://api-cn.faceplusplus.com/facepp/v3/detect",
            [
                'api_key'    => $this->faceApiKey,
                'api_secret' => $this->faceApiSecret,
                'image_url'  => $imageUrl,
                'return_landmark' => 2
            ]);
        $faces = json_decode($re)->faces;
        $token='';
        foreach ($faces as $key=>$face){
            $token = $face->face_token;
        }
        return $token;
    }

    /**
     * - 返回识别结果
     * @param $faceToken   //face_token
     * @return bool|string
     * @throws \CException
     */
    public function getFaceIdentifyResult($faceToken)
    {
        $params = [
            'age',
            'gender',
            'smiling',
            'skinstatus',
            'beauty',
            'ethnicity',
            'emotion',
            'headpose',
            'eyestatus',
            'mouthstatus'
        ];
        $re = HttpClient::from()->post("https://api-cn.faceplusplus.com/facepp/v3/face/analyze",
            [
                'api_key'        => $this->faceApiKey,
                'api_secret'     => $this->faceApiSecret,
                'face_tokens'    => $faceToken,
                'return_attributes'=> implode(',',$params)
            ]);
        return $re;
    }

}