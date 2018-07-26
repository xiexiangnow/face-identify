<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\EnumConfig;
use App\Services\QiNiuApi;
use App\Services\ThirdFaceApi;
use HttpClient;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use React\EventLoop\Factory;
use React\HttpClient\Client;

class uploadController extends Controller
{
    private $thirdFaceApiService;

    private $qiNiuApiService;

    private $enumConfigService;

    //七牛云图片上传配置
    const  ACCESS_KEY = 'byJc-8owLt9G5ljXsNYbTsfOCKzQgTV4UTPu3sCG';            //私钥AK
    const  SECRET_KEY = '6ybTxRxyDGndimpVMgK_k-6s1prGIPkGtB0Xolyb';            //私钥SK
    const  QINIU_BUCKET = 'op-zbj';                                            //空间名（对象存储中）
    const  BASE_URL  = 'http://ohsllkayi.bkt.clouddn.com';                     //外链默认域名（对象存储->内容管理）
    const  PIC_PREFIX = 'face_';

    //人脸识别配置
    const FACE_API_KEY    = "20DkOt0-yR8QIvVvD0LksMeeA_kBh34Q";                //调用此API的API Key
    const FACE_API_SECRET = "ztlVtEkalxsnHU051MYpyqaTfQsm4n1j";                //调用此API的API Secret

    public function __construct()
    {
        $this->enumConfigService = new EnumConfig();

        $this->thirdFaceApiService = new ThirdFaceApi(self::FACE_API_KEY, self::FACE_API_SECRET);

        $this->qiNiuApiService = new QiNiuApi(
            self::ACCESS_KEY,
            self::SECRET_KEY,
            self::QINIU_BUCKET,
            self::BASE_URL,
            self::PIC_PREFIX);
    }

    /**
     * - 验证页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \CException
     */
    public function index()
    {
        return view('upload-index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function picList()
    {
        $prams = [
            'lists' => Image::getAll()
        ];
        return view('show-page',$prams);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $params = [
            'detail' => Image::getDetail($id)
        ];
        return view('pic-detail',$params);
    }

    /**
     * - 执行验证
     * @throws \CException
     * @throws \Exception
     */
    public function upload()
    {
        $picPath = $this->qiNiuApiService->upload($_FILES['fileList']['name'], $_FILES['fileList']['tmp_name']);
        $token = $this->thirdFaceApiService->getFaceToken($picPath);
        $result = $this->thirdFaceApiService->getFaceIdentifyResult($token);

        //结果处理
        //文档地址：https://console.faceplusplus.com.cn/documents/4888383
        $faces = json_decode($result)->faces;
        if(empty($faces) || !$faces){
            echo "请上传人物图片！";
            exit;
        }
        $age ='';
        $ethnicity ='';
        $sex ='';
       foreach ($faces as $face){
           $sex = $this->enumConfigService->getChineseValue('gender',$face->attributes->gender->value);
           $ethnicity = $this->enumConfigService->getChineseValue('ethnicity',$face->attributes->ethnicity->value);
           $age =$face->attributes->age->value;
           $male_score = $face->attributes->beauty->male_score;
           $female_score = $face->attributes->beauty->female_score;

           $surgical_mask_or_respirator = $face->attributes->mouthstatus->surgical_mask_or_respirator;
           $other_occlusion = $face->attributes->mouthstatus->other_occlusion;
           $close = $face->attributes->mouthstatus->close;
           $open = $face->attributes->mouthstatus->open;

           $health = $face->attributes->skinstatus->health;
           $stain = $face->attributes->skinstatus->stain;
           $acne = $face->attributes->skinstatus->acne;
           $dark_circle = $face->attributes->skinstatus->dark_circle;

           $pitch_angle = $face->attributes->headpose->pitch_angle;
           $roll_angle = $face->attributes->headpose->roll_angle;
           $yaw_angle = $face->attributes->headpose->yaw_angle;

           $smileValue = $face->attributes->smile->value;
           $threshold  = $face->attributes->smile->threshold;

           $face_top =  $face->face_rectangle->top;
           $face_width =  $face->face_rectangle->width;
           $face_left =  $face->face_rectangle->left;
           $face_height =  $face->face_rectangle->height;

           $glass =  $face->attributes->glass->value;

           $anger =  $face->attributes->emotion->anger;
           $disgust =  $face->attributes->emotion->disgust;
           $fear =  $face->attributes->emotion->fear;
           $happiness =  $face->attributes->emotion->happiness;
           $neutral =  $face->attributes->emotion->neutral;
           $sadness =  $face->attributes->emotion->sadness;
           $surprise =  $face->attributes->emotion->surprise;
       }

        $html = '';
        $html .= "<div style='font-size: 12px'>";

        //年龄
        $html .= "<p>年龄：</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>年龄：</b>";
        $html .= "<b style='color:#EC681A;'>".$age."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //性别
        $html .= "<p>性别：</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>性别：</b>";
        $html .= "<b style='color:#EC681A;'>".$sex."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //人种
        $html .= "<p>人种：</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>人种：</b>";
        $html .= "<b style='color:#EC681A;'>".$ethnicity."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //情绪
        $html .= "<p>情绪识别结果：（范围 [0,100]，每个字段的返回值越大，则该字段代表的状态的置信度越高，字段值的总和等于 100）</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>愤怒值：</b>";
        $html .= "<b style='color:#EC681A;'>".$anger."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>厌恶值：</b>";
        $html .= "<b style='color:#EC681A;'>".$disgust."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>恐惧值：</b>";
        $html .= "<b style='color:#EC681A;'>".$fear."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>高兴值：</b>";
        $html .= "<b style='color:#EC681A;'>".$happiness."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>平静值：</b>";
        $html .= "<b style='color:#EC681A;'>".$neutral."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>伤心值：</b>";
        $html .= "<b style='color:#EC681A;'>".$sadness."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>惊讶值：</b>";
        $html .= "<b style='color:#EC681A;'>".$surprise."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //颜值
        $html .= "<p>颜值识别结果：（范围 [0,100]，小数点后 3 位有效数字）</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>男性参考值，越大颜值越高：</b>";
        $html .= "<b style='color:#EC681A;'>".$male_score."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>女性参考值，越大颜值越高：</b>";
        $html .= "<b style='color:#EC681A;'>".$female_score."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //是否佩戴眼镜
        $html .= "<p>是否佩戴眼镜的分析结果：</p>";
        $html .= "<ul>";
        if($glass == 'None') {
            $html .= "<li style='padding: 0;color: #b94a48;'>";
            $html .= "<b style='color: #00A2D2'>没有佩戴眼镜</b>";
            $html .= "</li>";
        }elseif($glass == 'Dark'){
            $html .= "<li style='padding: 0;color: #b94a48;'>";
            $html .= "<b style='color: #00A2D2'>佩戴墨镜</b>";
            $html .= "</li>";
        }else{
            $html .= "<li style='padding: 0;color: #b94a48;'>";
            $html .= "<b style='color: #00A2D2'>佩戴普通眼镜</b>";
            $html .= "</li>";
        }
        $html .= "</ul>";

        //笑容分析结果
        $html .= "<p>人脸姿势分析结果：（每个属性的值为一个 [-180, 180] 的浮点数,单位为角度）</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>数值越大表示笑程度高,[0,100]的浮点数：</b>";
        $html .= "<b style='color:#EC681A;'>".$smileValue."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>代表笑容的阈值，超过该阈值认为有笑容：</b>";
        $html .= "<b style='color:#EC681A;'>".$threshold."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //人脸矩形框的位置
        $html .= "<p>人脸矩形框的位置：</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>矩形框左上角像素点的纵坐标：</b>";
        $html .= "<b style='color:#EC681A;'>".$face_top."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>矩形框左上角像素点的横坐标：</b>";
        $html .= "<b style='color:#EC681A;'>".$face_left."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>矩形框的宽度：</b>";
        $html .= "<b style='color:#EC681A;'>".$face_width."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>矩形框的高度：</b>";
        $html .= "<b style='color:#EC681A;'>".$face_height."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //人脸姿势分析结果
        $html .= "<p>人脸姿势分析结果：（每个属性的值为一个 [-180, 180] 的浮点数,单位为角度）</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>抬头：</b>";
        $html .= "<b style='color:#EC681A;'>".$pitch_angle."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>旋转（平面旋转）：</b>";
        $html .= "<b style='color:#EC681A;'>".$roll_angle."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>摇头：</b>";
        $html .= "<b style='color:#EC681A;'>".$yaw_angle."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //面部特征识别
        $html .= "<p>面部特征识别信息：（范围 [0,100]，每个字段的值越大，则该字段代表的状态的置信度越高）</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>健康：</b>";
        $html .= "<b style='color:#EC681A;'>".$health."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>色斑：</b>";
        $html .= "<b style='color:#EC681A;'>".$stain."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>青春痘：</b>";
        $html .= "<b style='color:#EC681A;'>".$acne."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>黑眼圈：</b>";
        $html .= "<b style='color:#EC681A;'>".$dark_circle."</b>";
        $html .= "</li>";
        $html .= "</ul>";

        //嘴部状态信息
        $html .= "<p>嘴部状态信息：（范围 [0,100]，字段值的总和等于 100）</p>";
        $html .= "<ul>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>嘴部被医用口罩或呼吸面罩遮挡的置信度：</b>";
        $html .= "<b style='color:#EC681A;'>".$surgical_mask_or_respirator."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>嘴部被其他物体遮挡的置信度：</b>";
        $html .= "<b style='color:#EC681A;'>".$other_occlusion."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>嘴部没有遮挡且闭上的置信度：</b>";
        $html .= "<b style='color:#EC681A;'>".$close."</b>";
        $html .= "</li>";
        $html .= "<li style='padding: 0;color: #b94a48;'>";
        $html .= "<b style='color: #00A2D2'>嘴部没有遮挡且张开的置信度：</b>";
        $html .= "<b style='color:#EC681A;'>".$open."</b>";
        $html .= "</li>";
        $html .= "</ul>";


        $html .= "</div>";
       echo $html;

        if($picPath){
            $insertRe = Image::insertImage(['src' => $picPath,'description' => $html]);
            if(!$insertRe){
                echo "数据储存失败";
                exit;
            }
        }

    }


}
