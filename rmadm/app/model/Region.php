<?php

use \Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $_tableName = 'tb_region';

    /**
     * 获取区域
     * @param string $selectedProvince
     * @param string $selectedCity
     * @return array
     */
    public function getRegions($selectedProvince = '', $selectedCity = '')
    {
        $data = $this->getAll([
            'order' => 'province',
        ]);
        $ret = [];
        foreach ($data as $value) {
            if ($value['level'] == 2) {
                $ret[$value['province']]['data'] = $value;
                $value['selected'] = $selectedProvince == $value['name'] ? true : false;
            } elseif ($value['level'] == 3) {
                $ret[$value['province']]['children'][] = $value;
                $value['selected'] = $selectedCity == $value['name'] ? true : false;
            }
        }

        return $ret;
    }

    /**
     * 获取省
     */
    public function getProvince() {
        $data = $this->getAll([
            'where' => [
                'level' => 2,
            ],
        ]);

        return Func::pickArrayField($data, 'name', 'province');
    }

    /**
     * 获取市
     * @param $province
     * @return array
     */
    public function getCity($province) {
        $data = $this->getAll([
            'where' => [
                'province' => $province,
                'level' => 3,
            ],
        ]);

        return Func::pickArrayField($data, 'name', 'city');
    }
}