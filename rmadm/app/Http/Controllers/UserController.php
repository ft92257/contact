<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    protected $_authlevel = 0;

    public function __construct() {
        parent::__construct();

        $this->model = \User::m();
    }

    /**
     * 用户列表
     */
    public function indexAction() {
        $this->_getPageList();
    }

    /**
     * 编辑用户
     */
    public function editAction() {
        $uid = request('uid');
        $user = $this->model->getById($uid, 'uid');
        if (empty($user)) {
            $this->error('没有该用户');
        }

        $user['password'] = '';

        $industry = \UserIndustry::m()->getAll([
            'fields' => 'industry_id',
            'where' => [
                'uid' => $uid,
                'type' => \UserIndustry::TYPE_BELONG,
                'status' => 0,
            ],
        ]);
        $user['industry'] = \Func::pickArrayField($industry, 'industry_id');
        $user['industry'] = join(',', $user['industry']);

        $user['experience'] = $this->getExprience($uid);
        $user['education'] = $this->getEducation($uid);

        if ($this->isPost()) {
            if ($this->model->doSubmit($uid)) {
                return $this->success('更新成功！', \Func::KV($_SERVER, 'HTTP_REFERER'));
            } else {
                $this->error($this->model->errorMessage());
            }

        } else {
            $this->_display_form($user);
        }
    }

    protected function getExprience($uid) {
        $data = \UserExperience::m()->getAll([
            'where' => ['uid' => $uid, 'status' => 0],
        ]);
        $html = '';
        foreach ($data as $k => $value) {
            $html .= '<div style="height:42px;"><input type="text" style="width:120px;" name="experience['.$value['id'].'][enter_time]" value="'.$value['enter_time'].'" /> - ';
            $html .= '<input type="text" style="width:120px;" name="experience['.$value['id'].'][leave_time]" value="'.$value['leave_time'].'" /> &nbsp;&nbsp; ';
            $html .= '<input type="text" name="experience['.$value['id'].'][company]" value="'.$value['company'].'" /> &nbsp;&nbsp; ';
            $html .= '<input type="text" name="experience['.$value['id'].'][position]" value="'.$value['position'].'" /></div>';
        }

        return $html;
    }

    protected function getEducation($uid) {
        $data = \UserEducation::m()->getAll([
            'where' => ['uid' => $uid, 'status' => 0],
        ]);
        $html = '';
        foreach ($data as $k => $value) {
            $html .= '<div style="height:42px;"><input type="text" style="width:120px;" name="education['.$value['id'].'][enter_time]" value="'.$value['enter_time'].'" /> - ';
            $html .= '<input type="text" style="width:120px;" name="education['.$value['id'].'][leave_time]" value="'.$value['leave_time'].'" /> &nbsp;&nbsp; ';
            $html .= '<input type="text" name="education['.$value['id'].'][school]" value="'.$value['school'].'" /> &nbsp;&nbsp; ';
            $html .= '<input type="text" name="education['.$value['id'].'][specialty]" value="'.$value['specialty'].'" /></div>';
        }

        return $html;
    }

    public function getCityAction() {
        $province = request('upid');
        $data = \Region::m()->getCity($province);
        if (empty($data)) {
            die('');
        }
        $html = '<option value="0"></option>';
        foreach($data as $key => $value) {
            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }

        die($html);
    }
}
