<?php

namespace qzxy;

class Turn { /* 当页面刷新跳回后要加载的动作 */
    public static function echo($word) {
        session_start();
        $lel = $_SESSION['lel'];
        $_SESSION['lel'] = '';
        session_write_close();
        echo "<div class='poptip " . $lel . " shadow1'>" . $word . "</div>";
    }
}