<?php
define('SITE' , $_SERVER['HTTP_HOST']);
    include  __DIR__.'/int_lang.php';
    include __DIR__.'/install_header.php';
    include __DIR__.'/install_class.php';
    if (!isset($_COOKIE['progress'])){
        all::start();
    }
    if (isset($_COOKIE['progress']) && $_COOKIE['progress'] =='1' ){
        all::dbset();
    }
    if (isset($_COOKIE['progress']) && $_COOKIE['progress'] =='2'){
        all::checkev();
    }
    if (isset($_COOKIE['progress']) && $_COOKIE['progress'] =='3'){
        all::insert();
    }
    if (isset($_COOKIE['progress']) && $_COOKIE['progress'] =='4'){
        all::addadmin();
    }
    if (isset($_COOKIE['progress']) && $_COOKIE['progress'] =='5'){
        all::lockinstall();
    }

    include __DIR__.'/install_footer.php';

