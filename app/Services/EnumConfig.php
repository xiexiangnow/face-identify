<?php
namespace App\Services;

use Mockery\Exception;

class EnumConfig {

    public $params = [
        'gender' => [
            'Male'   => '男性',
            'Female' => '女性'
        ],
        'ethnicity' => [
            'ASIAN' => '亚洲人',
            'WHITE' => '白人',
            'BLACK' => '黑人'
        ]
    ];

    /**
     * @param $type
     * @param $value
     * @return string
     */
    public function getChineseValue($type, $value)
    {
        try {
            if (!$type || !$value) return null;
            return $this->params[$type][$value];
        }catch (\Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    
}