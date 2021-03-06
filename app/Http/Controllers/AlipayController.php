<?php

namespace App\Http\Controllers;

use App\Helpers\alipay\aop\AlipayMobilePublicMultiMediaClient;
use App\Helpers\alipay\pagepay\buildermodel\AlipayTradePagePayContentBuilder;
use App\Helpers\alipay\pagepay\service\AlipayTradeService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AlipayController extends Controller
{

    public $config = array (
        //应用ID,您的APPID。
        'app_id' => "2016091700532012",

        //商户私钥
        'merchant_private_key' => "MIIEpAIBAAKCAQEA6OuXTAz1fMwz+GWxONxRvmrX7dnJh5YP8NIhhuM6kUwaNfQrhqnz7Dm6Rkw4uo6m4Q2KXZY6rA4CByG2AOv2sr2c6sEKhekHUMJf4R18Z07g1pK7GQ2YQQ0XMvwNXEL+jN29QMU39ikIaoGy+Dicx35V5PYZ9vb+ck4EPRhDvuAxYfmYHATOSP8zVk1zALOwtv7ZBgCPQIlex4Qka8qgtXxSdgHGjIUAhnz40HYrGRARiuoK+8GS/juxwtN4Sh1Fgn8EJMuuHb9zUd5Y9SMe4F7kEvlNN9iuj/BPOkjwdGrGbt2rCINEkN3Zw6spm64+V0BlcOTbEqkOijaDuI2CdQIDAQABAoIBAF7hwcNIMCSDZtRUUKpSDbac/ZM1ucPS3HGEmAXDwNL5hl/eNHDqAKSFK52BZUaR3+cjxe6zyPjXx/mxwNuFQ/yyAx8aPjgookNCux4QDeJjnnGqWi8te41cUMwDI0onPFyT44lkDZToSDZi2U4Gec9GZqUbn54cJbDYmR9uKAKzuFR+TfUv3D1gHztwTM8scC7xWtv/c2Nbqkxd5I4uBzUpXlj5VUlo6ZvbAelxUyPjW9+vM5FlhBOfqVBhUvPaz90qCnjoQO8mISyl/CaZryEGIV4EwzAUmrOcehR/eexF6BsbtfpXAEwi7AQSKpnisTxVi+NXo17bGhILBfyhXQ0CgYEA9wTHUofhGoVBVhb4r9Xsv94Cdui9QW00laD5pl4hGYZMNYA3LhOquSfZ/VwRVkXJeTE/T8pJrnZEB9hX3QrDMz3DxmYjIuV0eD13nT1ESdM7pZC+31Bxu9Vt5z+5yT85sudHPmX7JhiXF11HZ7o+xSpnC2V+1/zuROCtSpqx0G8CgYEA8WOWEhv69D1KHjovrdE2bgUcFjGvSHeQJUDj1a/4n/EJFxkQKXYs5fJcPHRuEdQ9TjnlxNMVA9kGy8FlpOvQXXdkOUHviIzeOk/yspV07tHsK5aG8E63jmBrDNM4Kp1DhaY3h6WZtXo/6UsbYcwioAyTAQexKh4J8ke+3Ty9xVsCgYBnIpyiTc9jxk1wR2kP6W7O8T/wK96RCaqR6sMxfk2tnZAGKoFfgKCgbA4tJZqrfbnQGwrHIru+1uwyplaRGORFab1rAcwbztfhODDP+vufI03dI+E91hWWilc33TiR2Q8bLktltyi0UEINZEni+jUpFzos3PSn85f8NB7Gbm5diwKBgQCA1DaGybog2gkRotpJeSwEgeOgkLnNAkrDJyOxqy4VJo5EbpLqnfCOdM/3T+hiyZRCiLHxXvqLSCvRWRFHeLeG+q5ZxK/zf8Mm+f48g3mZ8B2MkdTIsipS6XCYsq36SF5+GNzwH1iuu2UavIQLqOgd5Tgbx6AtK0UBsnrSFpXtmwKBgQCR85hhrNWf8v+sB7z3KPSNHuSI7ZAMDpWMydCX2jX+BGFoc+GiROvPk9GyCisjPVXnTkhWbqcuXuO6dE2iS1wjvUz7x+i4vjKPFXFWLeRChuomjkNzWbLAvAt/bKyV8n154lT0E+WF16OuI6ptQ1c5MWu86cu218Kt4giDHDn9Hw==",

        //异步通知地址
        'notify_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/notify_url.php",

        //同步跳转
        'return_url' => "http://外网可访问网关地址/alipay.trade.page.pay-PHP-UTF-8/return_url.php",

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "
		MIIEpAIBAAKCAQEA6OuXTAz1fMwz+GWxONxRvmrX7dnJh5YP8NIhhuM6kUwaNfQrhqnz7Dm6Rkw4uo6m4Q2KXZY6rA4CByG2AOv2sr2c6sEKhekHUMJf4R18Z07g1pK7GQ2YQQ0XMvwNXEL+jN29QMU39ikIaoGy+Dicx35V5PYZ9vb+ck4EPRhDvuAxYfmYHATOSP8zVk1zALOwtv7ZBgCPQIlex4Qka8qgtXxSdgHGjIUAhnz40HYrGRARiuoK+8GS/juxwtN4Sh1Fgn8EJMuuHb9zUd5Y9SMe4F7kEvlNN9iuj/BPOkjwdGrGbt2rCINEkN3Zw6spm64+V0BlcOTbEqkOijaDuI2CdQIDAQABAoIBAF7hwcNIMCSDZtRUUKpSDbac/ZM1ucPS3HGEmAXDwNL5hl/eNHDqAKSFK52BZUaR3+cjxe6zyPjXx/mxwNuFQ/yyAx8aPjgookNCux4QDeJjnnGqWi8te41cUMwDI0onPFyT44lkDZToSDZi2U4Gec9GZqUbn54cJbDYmR9uKAKzuFR+TfUv3D1gHztwTM8scC7xWtv/c2Nbqkxd5I4uBzUpXlj5VUlo6ZvbAelxUyPjW9+vM5FlhBOfqVBhUvPaz90qCnjoQO8mISyl/CaZryEGIV4EwzAUmrOcehR/eexF6BsbtfpXAEwi7AQSKpnisTxVi+NXo17bGhILBfyhXQ0CgYEA9wTHUofhGoVBVhb4r9Xsv94Cdui9QW00laD5pl4hGYZMNYA3LhOquSfZ/VwRVkXJeTE/T8pJrnZEB9hX3QrDMz3DxmYjIuV0eD13nT1ESdM7pZC+31Bxu9Vt5z+5yT85sudHPmX7JhiXF11HZ7o+xSpnC2V+1/zuROCtSpqx0G8CgYEA8WOWEhv69D1KHjovrdE2bgUcFjGvSHeQJUDj1a/4n/EJFxkQKXYs5fJcPHRuEdQ9TjnlxNMVA9kGy8FlpOvQXXdkOUHviIzeOk/yspV07tHsK5aG8E63jmBrDNM4Kp1DhaY3h6WZtXo/6UsbYcwioAyTAQexKh4J8ke+3Ty9xVsCgYBnIpyiTc9jxk1wR2kP6W7O8T/wK96RCaqR6sMxfk2tnZAGKoFfgKCgbA4tJZqrfbnQGwrHIru+1uwyplaRGORFab1rAcwbztfhODDP+vufI03dI+E91hWWilc33TiR2Q8bLktltyi0UEINZEni+jUpFzos3PSn85f8NB7Gbm5diwKBgQCA1DaGybog2gkRotpJeSwEgeOgkLnNAkrDJyOxqy4VJo5EbpLqnfCOdM/3T+hiyZRCiLHxXvqLSCvRWRFHeLeG+q5ZxK/zf8Mm+f48g3mZ8B2MkdTIsipS6XCYsq36SF5+GNzwH1iuu2UavIQLqOgd5Tgbx6AtK0UBsnrSFpXtmwKBgQCR85hhrNWf8v+sB7z3KPSNHuSI7ZAMDpWMydCX2jX+BGFoc+GiROvPk9GyCisjPVXnTkhWbqcuXuO6dE2iS1wjvUz7x+i4vjKPFXFWLeRChuomjkNzWbLAvAt/bKyV8n154lT0E+WF16OuI6ptQ1c5MWu86cu218Kt4giDHDn9Hw==",
        'format' => "json"
    );

    public function payTest()
    {
        $buider = new AlipayTradePagePayContentBuilder();
        $buider->setOutTradeNo('111');
        var_dump($buider->getOutTradeNo());
    }

}
