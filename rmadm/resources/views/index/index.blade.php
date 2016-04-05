@extends('layouts.main')

@section('content')
<div class="z_main">
    <div class="z_location">
        <a href="#">首页</a><em>&gt;</em><span>用户列表</span>
    </div>
    <div class="z_tit">
        <div class="fr">
            <a href="app/dbms/xunjia_info_add.html" class="btn btn-primary btn-sm mr5" target="navTab" data-opt="{tabid:'dbms_xunjia_info_add'}">新增</a>
            <a href="#" class="btn btn-default btn-sm mr5"><i class="btn-icon system-fen"></i>分配归属</a>
            <a href="#" class="btn btn-default btn-sm"><i class="btn-icon system-expore"></i>导出EXCEL</a>
        </div>
        <strong>用户管理</strong>
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