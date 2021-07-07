@extends('base/app')

<?php
$pageId='index';
// $keywords = '';
?>

@section('meta')
    {{-- <meta name="baidu-site-verification" content="niIZ29Wkep"/> --}}
@stop

@section('content')
<div class="content">
    <!-- frame one -->

    <section class="intro-frame bg-dark">
        {{-- <h2>酒店常旅客</h2> --}}
        <h3>酒店常客计划</h3>
        <div class="text">
            希尔顿HHonors Gold Elite、SPG一夜升金
        </div>
        <img src="/static/img/screen-intro-530x918-h5-1.png" width="265" height="459" alt=""/>
    </section>

    <section class="intro-frame">
        {{-- <h2>信用卡</h2> --}}
        <h3>玩转信用卡</h3>
        <div class="text">
            VISA Signature御玺卡、Master世界卡玩6
        </div>
        <img src="/static/img/screen-intro-530x918-h5-2.png" width="265" height="459" alt=""/>
    </section>

    <!-- frame two -->
    <section class="intro-frame bg-dark">
        {{-- <h2>机票</h2> --}}
        <h3>里程机票</h3>
        <div class="text">
            美国往返享双倍"亚洲万里通"里程
        </div>

        <img src="/static/img/screen-intro-530x918-h5-3.png" width="265" height="459" alt=""/>
    </section>

    <!-- frame three -->


</div>


@stop
