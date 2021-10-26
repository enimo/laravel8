<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <meta name="applicable-device" content="mobile">
    <!-- <meta name="apple-itunes-app" content="app-id=000000, app-argument=yangmao://view/123"> -->
    <meta name="format-detection" content="telephone=no" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @section('meta')
    @show
    <title>{{ $title ?? '微搭APP - 给年轻生活加点味' }}</title>

    <meta content="{{@$keywords  ??  '微搭，信用卡，机票，理财，航空，酒店，常客计划'}}" name="keywords"/>
    <meta content="{{@$description  ?? '微搭，给年轻人的生活更多乐趣'}}" name="description"/>
    <link rel="stylesheet" href="/static/lib/ionic/css/ionic.custom.css"/>
    <link rel="stylesheet" href="/static/css/base.css"/>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />

    <?php
        $openInApp = true;
        if(!isset($_GET['fr']) || $_GET['fr'] !== 'appinner') {
            // $openInApp = false; //暂时取消底部bar的显示 - enimo 20210707
        }
    ?>

    <?php
        // 在APP内预览页面时，不显示底部下载栏
        if(!$openInApp) {
    ?>
    <style>
        body {
          padding-bottom: 60px;
        }
        .about-us {
            color: #B01C2B !important;
            text-decoration: none;
            background-color: #FCFBFB;
            padding: 2px 4px;
             opacity: 0.8; 
            border-radius: 4px;
            float: right;
            font-size: 12px;
            font-weight: normal;
            margin-top: -4px;
            margin-left: 10px;
            margin-right: -4px;
            display: inline-block;
        }
    </style>
    <?php
        }
    ?>
</head>
<body class="page">

@section('header')
    <!-- header -->
    <div class="bar bar-header bar-light">
        <div class="row">
            <div class="col col-20 logo-wrap">
                <div class="logo"></div>
            </div>
            <div class="col title-wrap">
                <div class="title-main">微搭APP</div>
                <div class="title-sub">陪你玩转低代码
<!-- 
                    <?php if(!$openInApp) { ?>
                    <a class="about-us" href="/about?fr=topbar">关于</a>
                    <?php } ?>
                    <a class="about-us" target="_blank" href="/">开发者社区</a> -->
                </div>
            </div>
        </div>
    </div>

    <!-- sub header -->
     <div class="bar bar-subheader">
        <div class="tabs">
            <a href="/" class="tab-item @if($pageId=='index') active @endif">
                主页
            </a>
            <a href="/index" class="tab-item @if($pageId=='bbs') active @endif">
                开发者社区
            </a>
            
            <a href="/about" class="tab-item @if($pageId=='about') active @endif">
                关于我们
            </a>

            <a href="/user" class="tab-item @if($pageId=='user') active @endif">
                用户列表
            </a>
            
        </div>
    </div> 
@show

@yield('content')

    <section class="about-us-panel bg-dark">
        {{-- <ul class="contact-info">
            <li><i class="icon icon-sina"></i>微博：@微搭APP</li>
            <!-- <li><i class="icon icon-qq"></i>QQ讨论：123456</li> -->
            <li><i class="icon icon-mail"></i>反馈邮箱：contact@weda.com</li>
        </ul> --}}
        <hr/>
        <div class="company-info">
            <p>
                联系我们：contact@weda.com
            </p>
            <p>
                <?=date("Y")?> &copy; weda.cmcc.enimo.cn
            </p>
            <p>
                {{-- 京ICP备14014459号-1 --}}
            </p>
            
        </div>
    </section>

<?php
    // 在APP内预览页面时，不显示底部下载栏
    if(!$openInApp) {
?>
    @include('include/fixed_bottom_bar')
<?php
    }
?>
<div id="wx-download-mask">
    {{-- <img src="/static/img/wx_download_guide.png" alt=""/> --}}
    <div class="wx-download-guide"></div>
</div>



</body>
</html>
