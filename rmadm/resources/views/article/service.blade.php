@extends('layouts.main')

@section('content')
<div class="z_main">
    <div class="z_location">
        <a href="#">首页</a><em>&gt;</em><span>文章管理</span><em>&gt;</em><span>编辑</span>
    </div>

    <div class="z_tit">
        <strong>服务条款</strong>
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

