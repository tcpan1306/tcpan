<?php 

namespace Home\Controller;

/**
 * Description of TestController
 *17002810530
 * @author PR  
 */
class TestController extends \Think\Controller {

    public function sendSMS() {
              $flag = sendSMS('13075412875', ['code' => (string)  mt_rand(1000, 9999), 'product' => '叫人起床尿尿']);
    }
      
    public function sendEmail() {
        $flag = sendEmail('1037606804@qq.com', 'tcpan', '活到老学到老');
        dump($flag);
    }
}
