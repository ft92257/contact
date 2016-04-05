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
                $value['selected'] = $selectedProvince == $value['name'] ? true : false;
                $ret[$value['province']]['data'] = $value;
            } elseif ($value['level'] == 3) {
                $value['selected'] = $selectedCity == $value['name'] ? true : false;
                $ret[$value['province']]['children'][] = $value;
            }
        }

        return $ret;
    }

    /**
     * 获取区域名称
     * @param $province
     * @param $city
     */
    public function getRegionName($province, $city) {
        $region = '';
        if ($province) {
            $region .= $this->getField(['province' => $province, 'level' => 2], 'name');
        }
        if ($city) {
            $region .= ' ';
            $region .= $this->getField(['city' => $city, 'level' => 3], 'name');
        }

        return $region;
    }

}