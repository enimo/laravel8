@extends('base.app')

<?php 
    $pageId='essay';
	//echo json_encode($tplData);
    $title = $tplData['essay_title']. ' - 玩在当下';
	// $tplData['bottom_bar'] = array(
	// 	"main_title" => "查看更多信息"
	// );

?>

{{-- @section('header') --}}
{{--     <section class="wb-header row">
        <div class="col col-75">
            <a href="/">
            <span class="icon icon-wb-logo"></span>
            <span class="title">玩在当下</span>
            </a>
            <span class="sep">|</span>
            <span class="page-title">给年轻生活加点味</span>

        </div>
        <div class="col">
            <a href="http://www.licaimofang.com/getapp"
               class="button button-primary button-small download-app">下载App</a>
        </div>
    </section> --}}

{{-- @stop --}}

@section('content')

    <div class="worth-buying-detail-page">


        <div class="wb-detail-content">

            <div class="content-summary">
                {{-- <div class="thumbnail">
                    <img class="picture" src="{{ $tplData['attachment']['attachment_path'] }}"
                         alt=""/>
                </div> --}}
                <div class="info">
                    <div class="record title">
                        {{$tplData['essay_title']}}
                        {{-- 【{{$tplData['essay_author']}}】 --}}
                    </div>
                    {{-- <div class="record feature">
                       	{{$tplData['essay_quote']}}
                    </div> --}}
                    {{-- <div class="record time">
			             {{ $tplData['essay_updatetime'] }}
                    </div> --}}
                </div>
            </div>

            <div class="sep"></div>

            <div class="desc">{!! $tplData['essay_content'] !!}</div>
        </div>
	<br>

    @if(strpos($tplData['essay_source'], 'http://')  !== false)
        <a class="buy-link" target="_blank" href="http://ym.enimo.cn/getapp?fr=h5-detail">
		{{-- @if($tplData['essay_essaytype'] == '1') --}}
			下载APP，{{$tplData['essay_essaytype']}}
		{{-- @elseif($tplData['essay_essaytype'] == '2') --}}
		{{-- @endif --}}
	       <span class="arrow-right">></span>
        </a>
    @endif
    </div>
@stop
