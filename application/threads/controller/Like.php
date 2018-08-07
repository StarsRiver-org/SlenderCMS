<?php
namespace qzxy\threads\Controller;
use qzxy\Thread;
use think\Controller;

class Like extends Controller{
    public function main(){
        return Thread::like($_GET['id']);
    }
}