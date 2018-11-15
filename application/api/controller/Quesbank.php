<?php
/**
 *      [Starsriver] (C)2014-2099.
 *      This is NOT a freeware, follows Apache2.0 licence
 *
 *      Author: 张宇
 *      Email:  starsriver@yahoo.com
 *      CreateDate:   2018-11-06
 *
 */
namespace qzxy\api\controller;

use qzxy\Qhelp;
use think\Controller;
use think\Db;


class Quesbank extends Controller {

    public function main() {
        if (empty($_POST['hash']) || (!empty($_POST['hash']) && $_POST['hash'] != SHASH)) {
            return Qhelp::json_en(['Stat' => 'error', "Message" => "您无权使用本接口"]);
        }
        return Qhelp::json_en([
            'Stat' => 'OK',
            'Data' => [
                'years' => self::getYears(@Qhelp::dss($_POST['course'])),
                'courses' => self::getCourses(),
                'chapters' => self::getChapters(@Qhelp::dss($_POST['year']), @Qhelp::dss($_POST['course'])),
                'type' => self::getType(@Qhelp::dss($_POST['year']), @Qhelp::dss($_POST['course']), @Qhelp::dss($_POST['chapter'])),
                'ques' => self::getQues(@Qhelp::dss($_POST['year']), @Qhelp::dss($_POST['course']), @Qhelp::dss($_POST['chapter']), @Qhelp::dss($_POST['type']), @Qhelp::dss($_POST['key']), @Qhelp::dss($_POST['page'])),
                ]
        ]);
    }

    protected function getCourses() {
        $course = [];
        $course_temp = Db::query("select `course` from tool_quesbank");
        foreach ($course_temp as $v) {
            if (!in_array($v['course'], $course)) {
                $course[] = $v['course'];
            }
        }
        return $course;
    }

    protected function getYears($course = null) {
        $years_temp = $years = [];
        if ($course) {
            $years_temp = Db::query("select `year` from tool_quesbank where course = '" . $course . "'");
        }
        foreach ($years_temp as $v) {
            if (!in_array($v['year'], $years)) {
                $years[] = $v['year'];
            }
        }
        return $years;
    }

    protected function getChapters($year = null, $course = null) {
        $cp_temp = $cp = [];
        if ($year && $course) {
            $cp_temp = Db::query("select chapter from tool_quesbank where `year` = '" . $year . "' AND course = '" . $course . "'");
        }
        foreach ($cp_temp as $v) {
            if (!in_array($v['chapter'], $cp)) {
                $cp[] = $v['chapter'];
            }
        }
        return $cp;
    }

    protected function getType($year = null, $course = null, $chapter = null) {
        $cp_temp = $cp = [];
        if ($year && $course) {
            $cp_temp = Db::query("select `type` from tool_quesbank where `year` = '" . $year . "' AND course = '" . $course . "' ".($chapter ? 'AND chapter = \''.$chapter.'\'' : '')."");
        }
        foreach ($cp_temp as $v) {
            if (!in_array($v['type'], $cp)) {
                $cp[] = $v['type'];
            }
        }
        return $cp;
    }

    protected function getQues($year = null, $course = null, $chapter = null, $type = null, $key = null, $start = 0) {
        $p = $pf = [];
        $intv = 15;
        $st = 0;
        if($start && Qhelp::chk_pint($start)){
            $st = $start*$intv;
        }
        if ($year && $course) {
            $p = Db::query("select * from tool_quesbank where `year` = '".$year."' AND course = '".$course."' ".($type ? 'AND type = \''.$type.'\'' : '').($chapter ? 'AND chapter = \''.$chapter.'\'' : '').($key ? 'AND ques like \'%'.$key.'%\'' : '')." ORDER by type DESC limit ".$st.','.$intv."");
        } elseif($key) {
            $p = Db::query("select * from tool_quesbank where ques like '%".$key."%'  ORDER by type DESC limit ".$st.','.$intv."");
        }
        foreach ($p as $v) {
            $pf[] = [
                'ques' => $v['ques'],
                'ans' => $v['ans'],
                'note' => $v['note'],
                'type' => $v['type'],
                'chos' => [
                    'A' => $v['A'], 'B' => $v['B'], 'C' => $v['C'], 'D' => $v['D'], 'E' => $v['E'],
                    'F' => $v['F'], 'G' => $v['G'], 'H' => $v['H'], 'I' => $v['I'], 'J' => $v['J'],
                    ],
                ];
        }
        return $pf;
    }
}