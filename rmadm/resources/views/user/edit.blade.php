@extends('layouts.main')

@section('content')
<div class="z_main">
    <div class="z_location">
        <a href="#">首页</a><em>&gt;</em><span>用户列表</span><em>&gt;</em><span>会员编辑</span>
    </div>

    <div class="z_tit">
        <strong>会员编辑</strong>
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

<script>
    getChildrenOptions('/user/getCity', $('[name=province]')[0], '{!! $data['city'] !!}');

    checkedTarget('card_verify', 2, $('[name=card_reason]').parent().parent());
</script>

@endsection

