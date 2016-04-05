@extends('layouts.main')

@section('content')
    <div class="z_main">
        <div class="z_location">
            <a href="#">首页</a><em>&gt;</em><span>客户列表</span>
        </div>
        <div class="z_tit">
            <div class="fr">
                <a href="app/dbms/xunjia_info_add.html" class="btn btn-primary btn-sm mr5" target="navTab" data-opt="{tabid:'dbms_xunjia_info_add'}">新增</a>
                <a href="#" class="btn btn-default btn-sm mr5"><i class="btn-icon system-fen"></i>分配归属</a>
                <a href="#" class="btn btn-default btn-sm"><i class="btn-icon system-expore"></i>导出EXCEL</a>
            </div>
            <strong>客户管理列表</strong>
        </div>
        <div class="z_bor css-form form-inline clearfix">
            <dl>
                <dt><label>客户名称</label></dt>
                <dd><input type="text"></dd>
            </dl>
            <dl>
                <dt><label>客户编号</label></dt>
                <dd><input type="text"></dd>
            </dl>
            <dl>
                <dt><label>状态</label></dt>
                <dd>
                    <div class="selectbox" style="width: 250px;"><div id="combox_9090486" class="select"><a href="javascript:" class="select" name="undefined" value="0">有效</a><i class="combox-icon"></i><select class="select">
                                <option selected="" value="0">有效</option>
                                <option value="1">选项二</option>
                                <option value="2">选项二</option>
                                <option value="3">选项三</option>
                                <option value="4">选项四</option>
                                <option value="5">选项五</option>
                            </select></div></div>
                </dd>
            </dl>
            <dl>
                <dt><label>人员</label></dt>
                <dd>
                    <div class="selectbox" style="width: 250px;"><div id="combox_2351511" class="select">
                            <a href="javascript:" class="select" name="undefined" value="0">行业信息部负责人</a><i class="combox-icon"></i>
                            <select class="select">
                                <option selected="" value="0">行业信息部负责人</option>
                                <option value="1">选项二</option>
                                <option value="2">选项二</option>
                                <option value="3">选项三</option>
                                <option value="4">选项四</option>
                                <option value="5">选项五</option>
                            </select></div></div>
                </dd>
            </dl>
            <dl>
                <dt>&nbsp;</dt>
                <dd><button class="btn btn-default">查询</button></dd>
            </dl>
        </div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="z_table">
            <colgroup>
                <col style="width:5%">
                <col style="width:9%;">
                <col style="width:12%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
                <col style="width:9%;">
            </colgroup>
            <tbody><tr>
                <th><span class="z_checkbox"><input type="checkbox" style="display: none;"></span></th>
                <th>客户ID</th>
                <th>客户名称</th>
                <th>创建人</th>
                <th>所属部门</th>
                <th>创建时间</th>
                <th>行业信息部负责人</th>
                <th>更新时间</th>
                <th>商务部负责人</th>
                <th>更新时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            <tr>
                <td><span class="z_checkbox"><input type="checkbox" style="display: none;"></span></td>
                <td>000001</td>
                <td><span class="pname"><a href="app/crm/crm_kehu_detail.html" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">上海一研生物科技有限公司</a></span></td>
                <td>张三</td>
                <td>研发部</td>
                <td>2015-10-10</td>
                <td>瑶瑶</td>
                <td>2015-10-10</td>
                <td>李四</td>
                <td>2015-10-10</td>
                <td>草稿</td>
                <td><a href="app/crm/crm_kehu_detail.html" class="mr10" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">查看详情</a></td>
            </tr>
            <tr>
                <td><span class="z_checkbox"><input type="checkbox" style="display: none;"></span></td>
                <td>000001</td>
                <td><span class="pname"><a href="app/crm/crm_kehu_detail.html" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">上海一研生物科技有限公司</a></span></td>
                <td>张三</td>
                <td>研发部</td>
                <td>2015-10-10</td>
                <td>瑶瑶</td>
                <td>2015-10-10</td>
                <td>李四</td>
                <td>2015-10-10</td>
                <td>草稿</td>
                <td><a href="app/crm/crm_kehu_detail.html" class="mr10" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">查看详情</a></td>
            </tr>
            <tr>
                <td><span class="z_checkbox"><input type="checkbox" style="display: none;"></span></td>
                <td>000001</td>
                <td><span class="pname"><a href="app/crm/crm_kehu_detail.html" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">上海一研生物科技有限公司</a></span></td>
                <td>张三</td>
                <td>研发部</td>
                <td>2015-10-10</td>
                <td>瑶瑶</td>
                <td>2015-10-10</td>
                <td>李四</td>
                <td>2015-10-10</td>
                <td>草稿</td>
                <td><a href="app/crm/crm_kehu_detail.html" class="mr10" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">查看详情</a></td>
            </tr>
            <tr>
                <td><span class="z_checkbox"><input type="checkbox" style="display: none;"></span></td>
                <td>000001</td>
                <td><span class="pname"><a href="app/crm/crm_kehu_detail.html" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">上海一研生物科技有限公司</a></span></td>
                <td>张三</td>
                <td>研发部</td>
                <td>2015-10-10</td>
                <td>瑶瑶</td>
                <td>2015-10-10</td>
                <td>李四</td>
                <td>2015-10-10</td>
                <td>草稿</td>
                <td><a href="app/crm/crm_kehu_detail.html" class="mr10" target="navTab" data-opt="{tabid:'crm_kehu_detail'}">查看详情</a></td>
            </tr>
            </tbody></table>
        <div class="pagingBar">
            <span class="disable">共 200 个</span><a href="javascript:;" class="paging-prev">上一页</a><a href="javascript:;" class="paging-number">1</a><a href="javascript:;" class="paging-number">2</a><span class="cur">3</span><a href="javascript:;" class="paging-number">4</a><a href="javascript:;" class="paging-number">5</a><a href="javascript:;" class="paging-number">6</a><a href="javascript:;" class="paging-number">7</a><a href="javascript:;" class="paging-number">8</a><a href="javascript:;" class="paging-number">9</a><a href="javascript:;" class="paging-number">10</a><a href="javascript:;" class="paging-next">下一页</a><span class="disable">3 / 10</span>
        </div>
    </div>
@endsection