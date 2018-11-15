<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2017-12-16
 *
 */
namespace qzxy;
use think\Controller;

class File extends Controller {
    public static function saveimg($name) { /*单独用于保存单个图像*/
        $file = request()->file($name);
        $step = $file->validate(['size' => 2048000, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'data/catch/temp/img');
        if ($step) {
            // echo $step->getExtension();
            // $tmp = $step->getFilename();
            $tmp = $step->getSaveName();
            return substr($tmp, 0, 8) . substr($tmp, 9);
        } else {
            return 0;
        }
    }

    public static function saveavt($name = 'avatar') {
        $avt = request()->file($name);
        if($avt){
            $info = $avt->validate(['size' => 20480000, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'data/catch/temp/avatar');
            if ($info) {
                $avtName = $info->getSaveName();
                $image = \think\Image::open(ROOT_PATH . 'data/catch/temp/avatar/'.$avtName);
                $image->thumb(240, 320)->save(ROOT_PATH . 'data/catch/temp/avatar/'.$avtName,null,100);
                return substr($avtName, 0, 8) . substr($avtName, 9);
            } else {
                return ['Stat' => 'error', "Message" => '图像上传失败：'.$avt->getError()];
            }
        } else {
            return null;
        }
    }

    public static function delimg($img) {
        @unlink(ROOT_PATH . 'data/catch/temp/img/' . substr($img, 0, 8) . '/' . substr($img, 8));
    }
    public static function delavt($img) {
        @unlink(ROOT_PATH . 'data/catch/temp/avatar/' . substr($img, 0, 8) . '/' . substr($img, 8));
    }

    public static function fetchimg($imglog){
        return DATA_ROOT . '/catch/temp/img/' . substr($imglog, 0, 8) . '/' . substr($imglog, 8);
    }

    public static function fetchavt($imglog){
        return DATA_ROOT . '/catch/temp/avatar/' . substr($imglog, 0, 8) . '/' . substr($imglog, 8);
    }
}