@extends('layouts.main')

@section('content')
<div class="z_main">
    <div class="z_location">
        <a href="#">首页</a><em>&gt;</em><span>动态列表</span>
    </div>
    <div class="z_tit">
        <strong>动态管理</strong>
    </div>

    <div class="z_bor css-form form-inline clearfix">
        <form action="" method="get">
        {!!  $searchHtml !!}
        </form>
    </div>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="z_table">
        {!!  $listHtml !!}
    </table>

    {!! $page !!}
</div>
@endsection