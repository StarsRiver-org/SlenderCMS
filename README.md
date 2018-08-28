# QzxyWebsite
给学校做的网站-我的第一个个人项目

#主要目录结构

WEB程序目录
├─application           应用目录
│  ├─common             公共模块目录（可以更改）
│  ├─module_name        模块目录
│  │  ├─index.php       模块控制器函数文件(可更改)
│  │  └─....php         模块函数文件
│  │ 
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
├─static                WEB本地资源（对外访问目录）
├─template              模板目录
│  └─default            默认模板目录
│    ├─common          
│    ├─index           
│    └─admin       
│
│─index.php          入口文件
└─.htaccess          用于apache的重写

# 使用说明
* 运行安装程序（SITE/install）
* 修改config/config.php 全局常量SITE为你的域名值。

# 功能
* 仅仅是一个简单。。。的内容管理网站
* 整合了阿里云短信发送功能

# 模块
* consoleboard //后台
* portal  //门户
* special  //专题
* 
* portal下有几个文件，由于数据库为空，所以几乎处于废弃状态。可以看index注释来熟悉怎么用。
* [这个是测试地址](http://qzxy.starsriver.club)
