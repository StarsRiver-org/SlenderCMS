<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-10-18
 *
 */
namespace qzxy;

class Filter {
    static function filt_array($array) {/*过滤数组中相同项*/

        $key = count($array);
        for ($i = 0; $i < $key; $i++) {
            if(isset($array[$i])){
                $currentitem = $array[$i];
                for ($p = $i + 1; $p < $key; $p++) {
                    if (isset($array[$p]) && $currentitem == $array[$p]) {
                        unset($array[$p]);
                    }
                }
            }
        }
        return array_filter($array);
    }

    static function sample($arr,$key) { /*数组参数采样*/
        $res = [];
        foreach ($arr as $k=>$value){
            $res[] = $value[$key];
        }
        return $res;
    }

}
