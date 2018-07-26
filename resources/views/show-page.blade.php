
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>人脸列表</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="free html5, free template, free bootstrap, html5, css3, mobile first, responsive" />

    <!-- Facebook and Twitter integration -->
    <meta property="og:title" content=""/>
    <meta property="og:image" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:site_name" content=""/>
    <meta property="og:description" content=""/>
    <meta name="twitter:title" content="" />
    <meta name="twitter:image" content="" />
    <meta name="twitter:url" content="" />
    <meta name="twitter:card" content="" />

    <!-- Animate.css -->
    <link rel="stylesheet" href="{{asset('assets/show-page/css/animate.css')}}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{asset('assets/show-page/css/magnific-popup.css')}}">
    <!-- Salvattore -->
    <link rel="stylesheet" href="{{asset('assets/show-page/css/salvattore.css')}}">
    <!-- Theme Style -->
    <link rel="stylesheet" href="{{asset('assets/show-page/css/style.css')}}">
    <!-- Modernizr JS -->
    <script src="{{asset('assets/show-page/js/modernizr-2.6.2.min.js')}}"></script>
    <!-- FOR IE9 below -->
    <!--[if lt IE 9]>
    <script src="{{asset('assets/show-page/js/respond.min.js')}}"></script>
    <![endif]-->

</head>
<body>
<div id="fh5co-main">
    <div class="container">
        <div class="row">
            <div id="fh5co-board" data-columns>
                @forelse($lists as $pic)
                <div class="item">
                    <div class="animate-box">
                        <a href="/upload/{{$pic->id}}"><img src="{{$pic->src}}" alt="Free HTML5 Bootstrap template"></a>
                        <div class="fh5co-desc">{{$pic->created_at}}</div>
                    </div>
                </div>
                @empty
                <p>暂无数据</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<!-- jQuery -->
<script src="http://www.jq22.com/jquery/jquery-1.10.2.js"></script>
<!-- jQuery Easing -->
<script src="{{asset('assets/show-page/js/jquery.easing.1.3.js')}}"></script>
<!-- Bootstrap -->
<script src="http://www.jq22.com/jquery/bootstrap-3.3.4.js"></script>
<!-- Waypoints -->
<script src="{{asset('assets/show-page/js/jquery.waypoints.min.js')}}"></script>
<!-- Magnific Popup -->
<script src="{{asset('assets/show-page/js/jquery.magnific-popup.min.js')}}"></script>
<!-- Salvattore -->
<script src="{{asset('assets/show-page/js/salvattore.min.js')}}"></script>
<!-- Main JS -->
<script src="{{asset('assets/show-page/js/main.js')}}"></script>
</body>
</html>
