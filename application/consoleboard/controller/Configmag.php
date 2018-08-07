<?php
    namespace qzxy\consoleboard\controller;

    use think\Controller;
    use qzxy\User;
    use qzxy\Qhelp;
    use think\Db;

    class Configmag extends Controller{
        public function _initialize() {
            new Init();
            if(!User::has_pm('config_mag')){
                $this->error('操作错误，您未获得该操作权限');
                return null;
            }
        }

        public function main(){

            $this->assign(['configmag'   => 'active',]);
            return view('admin/configmag');
        }

        public function logic(){
            if(empty($_POST)){
                return Qhelp::json_en(['Stat' => 'error', 'Message' => '页面无请求数据，无法理解该行为。']);
            }

            if(!empty($_POST['getconf'])){
                switch ($_POST['getconf']){
                    case 'base': return Configmag_function::getconf('Info') ;break;
                    case 'sms': return Configmag_function::getconf('SmsService') ;break;
                    case 'oth': return Configmag_function::getconf('Pm') ;break;
                }
            }

            if(!empty($_POST['data'])){

                $data = Qhelp::json_de($_POST['data']);
                /*
                 * $data = [
                 *      'name' => char,
                 *      'data' => str,
                 * ]
                 * */
                if(empty($data) || !isset($data['name'])){
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '无法确认数据，你可能需要刷新页面']);
                }

                /*******************************/
                if(!empty($_POST['saveconf'])){
                    return Configmag_function::saveconf($data);
                } elseif (!empty($_POST['resetconf'])){
                    return Configmag_function::resetconf($data);
                } else {
                    return Qhelp::json_en(['Stat' => 'error', 'Message' => '无法确认行为。你可能需要刷新页面']);
                }
                /********************************/
            }

            return Qhelp::json_en(['Stat' => 'error', 'Message' => '页面无请求行为，无法确认方法。']);
        }
    }
    