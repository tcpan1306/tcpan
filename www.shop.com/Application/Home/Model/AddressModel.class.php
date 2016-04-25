<?php


namespace Home\Model;
/**
 * Description of AddressModel
 *
 * @author PR
 */
class AddressModel extends \Think\Model{
    protected $_auto = [
        ['member_id','get_user_id',self::MODEL_INSERT,'function'],
    ];
    
    /**
     * 获取指定地址下的地址.
     * @param integer $parent_id 父级地址id
     * @return array
     */
    public function getListByParentId($parent_id=0){
        return M('Locations')->where(['parent_id'=>$parent_id])->select();
    }
    
     
    /**
     * 获取当前用户的收货地址列表.
     * @return array
     */
    public function getList(){
        $userinfo = session('MEMBER_INFO');
        $cond = [
            'member_id'=>$userinfo['id'],
        ];
        return $this->where($cond)->select();
    }
    
    public function addAddress() {
        if($this->data['is_default']){
             //将已存在地址改为非默认
            $userinfo = session('MEMBER_INFO');
            $cond = [
                'member_id'=>$userinfo['id'],
            ];
            $this->where($cond)->setField('is_default',0);
        }
        return $this->add();
    }
}
