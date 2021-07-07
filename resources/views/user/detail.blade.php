@extends('base.app')

<?php 
    $pageId='user';
	//echo json_encode($tplData);
    $title = $tplData['user']['name'].' - '.env('APP_NAME');
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
                        {{$tplData['user']['name']}}
                        {{-- 【{{$tplData['essay_author']}}】 --}}
                    </div>
                </div>
            </div>

            <div class="sep"></div>

            <div class="desc">
                
                {{$tplData['user']['email']}}
            </div>
        </div>
	<br>


    </div>
@stop
