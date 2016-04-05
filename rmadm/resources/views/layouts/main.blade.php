<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{$title}}</title>
    <link rel="stylesheet" href="{{$assets}}/css/index.css" rel="stylesheet">
    <link rel="stylesheet" href="{{$assets}}/css/ui-dialog.css" rel="stylesheet">

    <script src="{{$assets}}/js/jquery1.8.3.js"></script>
    <script type="text/javascript" src="{{ $assets }}/js/common.js"></script>
    <script type="text/javascript" src="{{ $assets }}/js/date/WdatePicker.js"></script>

    <!--[if lt IE 9]>
    <script src="{{$assets}}/js/html5shiv.js"></script>
    <script src="{{$assets}}/js/respond.min.js"></script>
    <![endif]-->
</head>
<body scroll="no">
<script>
    var URL_VALIDATE = '{{Func::U('autoValidate')}}';
</script>
<div class="wraper">
    <!--head-->

    <div class="ERP-M" style="top:0px;">
        <!--left-->

        <!--right-->
        <div class="ERP-R ERP-R-open" style="left: 0px;">
            <div class="content" id="navTab">
                <div class="navTab-panel">
                    <div class="page unitBox" style="display: block;">
                        <div class="ERP-R-M">
                            <i class="ERP-R-M-toogle system-toogle-stop"></i>
                            <!--二级页面导航 start-->
                            @include('layouts.sidebar')
                            <!--二级页面导航 end-->
                            <!--二级页面内容 start-->
                            <div class="ERP-R-M-R">
                                <div class="sub-content">
                                    @yield('content')
                                </div>
                            </div>
                            <!--二级页面内容 end-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!--bottom-->
    <div class="ERP-B" style="display: none;">
        <div class="tags">
            <div class="tags-wrap">
                <ul class="tags-list clearfix">
                    <li tabid="main"><a href="javascript:;">首页</a></li>
                </ul>
            </div>
            <div class="tags-more">
                <a href="#" class="tags-more-toggle">…</a>
                <ul class="tags-more-list tabsMoreList">
                    <li class="selected" tabid="main"><a href="#">首页</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--加载条-->
<!--
<div id="loadingBg"></div>
<div id="loadingBar">数据加载中，请稍等...</div>-->
<!--弹窗蒙板-->
<div id="dialogBackground" class="dialogBackground"></div>

<div id="dialogProxy" class="dialog dialogProxy" style="display: none;">
    <div class="dialogHeader">
        <div class="dialogHeader_r">
            <div class="dialogHeader_c">
                <a class="close" href="#close">close</a>
                <a class="maximize" href="#maximize">maximize</a>
                <a class="minimize" href="#minimize">minimize</a>
                <h1></h1>
            </div>
        </div>
    </div>
    <div class="dialogContent"></div>
    <div class="dialogFooter">
        <div class="dialogFooter_r">
            <div class="dialogFooter_c">
            </div>
        </div>
    </div>
</div>
<!--<script src="&lt;!&ndash;# echo var="RES_URL" &ndash;&gt;/jss_source/require.js?v=a.js"></script>
<script src="&lt;!&ndash;# echo var="RES_URL" &ndash;&gt;/jss_source/configs.js?v=1.js"></script>
<script>
    require.config({
        urlArgs:'v=a.js',
        baseUrl: '&lt;!&ndash;# echo var="RES_URL" &ndash;&gt;/jss_source/'
    });
    require(['jquery','page/index']);
</script>-->


<script src="{{$assets}}/js/modernizr.min.js"></script>
<script src="{{$assets}}/js/excanvas.js"></script>
<script src="{{$assets}}/js/jquery.plugin.min.js"></script>
<script src="{{$assets}}/js/artdialog-plus.js"></script>
<script src="{{$assets}}/js/echarts.min.js"></script>
<script src="{{$assets}}/js/jquery.validate.min.js"></script>
<script src="{{$assets}}/js/lang.js"></script>
<script src="{{$assets}}/js/validate.addmethods.js"></script>
<script src="{{$assets}}/js/validatorset.js"></script>
<script src="{{$assets}}/js/webuploader.min.js"></script>
<script src="{{$assets}}/js/history.js"></script>
<script src="{{$assets}}/js/database.js"></script>
<script src="{{$assets}}/js/code.js"></script>
<script src="{{$assets}}/js/plug.js"></script>
<script src="{{$assets}}/js/tree.js"></script>
<script src="{{$assets}}/js/navtab.js"></script>
<script src="{{$assets}}/js/dialog.js"></script>
<script src="{{$assets}}/js/ajax.js"></script>
<script src="{{$assets}}/js/combox.js"></script>
<script src="{{$assets}}/js/circle.js"></script>
<script src="{{$assets}}/js/extend.js"></script>

<!--<script src="&lt;!&ndash;# echo var="RES_URL" &ndash;&gt;/js/erpcode.min.js"></script>-->

<script type="text/javascript">
    $(function(){
        MOB.init();
    });
</script>
</body>
</html>