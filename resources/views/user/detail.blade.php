@extends('base.app')

<?php 
    $pageId='about';
	//echo json_encode($tplData);
    $title = $tplData['essay_title'].' - '.env('APP_NAME');
	// $tplData['bottom_bar'] = array(
	// 	"main_title" => "查看更多信息"
	// );

?>

{{-- @section('header') --}}

{{-- @stop --}}

@section('content')

    <div class="worth-buying-detail-page">


        <div class="wb-detail-content">

            <div class="content-summary">
                <div class="info">
                    <div class="record title">
                        {{$tplData['essay_title']}}
                        {{-- 【{{$tplData['essay_author']}}】 --}}
                    </div>
                </div>
            </div>

            <div class="sep"></div>

            <div class="desc">{!! $tplData['essay_content'] !!}</div>
        </div>
	<br>


    </div>
@stop
