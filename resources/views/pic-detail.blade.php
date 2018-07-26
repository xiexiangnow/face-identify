<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <title>数据详情</title>
</head>
<style>
    .container {
        width: auto;
        height: auto;
        border: 1px #00a2d2 dashed;
        padding: 20px;
    }
    .container a {
        text-decoration: none;
        border: 1px #00a2d2 solid;
        padding: 5px 10px;
        color: #fff;
        background: #00a2d2;
        border-radius: 5px;
        font-weight: 300;
        font-size: 12px;
    }

    .container a:hover{
        text-decoration: none;
        border: 1px #EC681A solid;
        padding: 5px 10px;
        color: #fff;
        background: #EC681A;
        border-radius: 5px;
        font-weight: 300;
        font-size: 12px;
    }

    .pic img {
        width: 200px;
        height: auto;
        margin-top: 20px;
        border-radius: 5px;
    }

    .pic p{
        font-size: 12px;
    }
</style>
<body>
    <div class="container">
        <a href="/lists">返回列表</a>
        <a href="/">继续验证</a>
        <div class="pic">
            <img src="{{$detail->src}}" alt="">
            <p>生成时间：{{$detail->created_at}}</p>
        </div>
        {!! htmlspecialchars_decode($detail->description) !!}
    </div>
</body>
</html>