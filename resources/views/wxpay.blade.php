@extends('base/app')

<?php
$pageId='bbs';
// $keywords = '';
?>

@section('meta')
    {{-- <meta name="baidu-site-verification" content="niIZ29Wkep"/> --}}
@stop

@section('content')
<div class="content">
    <!-- frame one -->
    <!-- frame two -->
    <section class="intro-frame bg-dark">
        <h3>{{ $tplData['description']}} </h3>
        <h3>{{ $tplData['cent']}} 分 </h3>
        <div class="text">
            
            <a target="_blank" href="{{ $tplData['code']}}"
               class="">{{ $tplData['out_trade_no']}}</a>
        </div>

        <div id="qrcode-wrap" >
            <canvas id="qrcode-canvas" class="img-polaroid"></canvas>
        </div>

        <!-- <img src="/static/img/screen-intro-530x918-h5-3.png" width="265" height="459" alt=""/> -->
    </section>

    <!-- frame three -->


</div>
<script src="/static/js/qrcode.js"></script>

<script>
(function () {
    //'use strict';

    var url = "{{ $tplData['code'] }}";
	//qrcode handler
	var qrcodedraw = new qrcodelib.qrcodedraw();
    
	//triggered errors will throw
    qrcodedraw.errorBehavior.length = false;

    var drawQR = function(text){
      qrcodedraw.draw(document.getElementById('qrcode-canvas'),text,function(error,canvas){
        if(error) {
          console.log(error);
        }
      });
    }

	drawQR( url );

})();

</script>

@stop
