<?php

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

    static function de_json($url) { /*将json转为数组*/
        return json_decode(file_get_contents($url), true);
    }
    static function sample($arr,$key) { /*数组参数采样*/
        $res = [];
        foreach ($arr as $k=>$value){
            $res[] = $value[$key];
        }
        return $res;
    }

}
