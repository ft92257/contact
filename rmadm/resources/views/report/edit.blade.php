@extends('layouts.main')

@section('content')
<div class="z_main">
    <div class="z_location">
        <a href="#">首页</a><em>&gt;</em><span>举报列表</span><em>&gt;</em><span>编辑</span>
    </div>

    <div class="z_tit" >
        <strong>举报内容：</strong> <span>动态&nbsp;&nbsp;</span> <span style="color:#a4e3f3;">{{$data['source_id']}}</span>
    </div>

    <div style="border-top: 1px solid #ddd;padding: 10px 10px 15px 15px;">
        {{$data['source']['content']}}<br>
        @foreach($data['source']['images'] as $img)
            <img src="{{$img}}" width="160" />
        @endforeach
        <br><br>
        <a href="/dynamic/delete?id={{$data['id']}}"><button class="btn">删除</button></a>
    </div>

    <div class="z_tit">
        <strong>处理进度：</strong>
    </div>

    <form method="post" action="" enctype="multipart/form-data">
    <div class="edit-form z_bor">
        {!! $formHtml !!}
    </div>

    <div class="tc">
        <button class="btn btn-primary btn-lg"  type="submit">　保 存　</button>
    </div>
    </form>
</div>

@endsection

