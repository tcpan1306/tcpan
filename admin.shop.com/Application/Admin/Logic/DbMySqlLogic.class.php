<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Logic;

/**
 * Description of DbMySqlLogic
 *
 * @author PR
 */
class DbMySqlLogic implements DbMysqlInterface{
    public function connect() {
        echo __METHOD__ .' <br/>';
    }

    public function disconnect() {
         echo __METHOD__ .' <br/>';
    }

    public function free($result) {
         echo __METHOD__ .' <br/>';
    }

    public function getAll($sql, array $args = array()) {
         echo __METHOD__ .' <br/>';
    }

    public function getAssoc($sql, array $args = array()) {
         echo __METHOD__ .' <br/>';
    }

    public function getCol($sql, array $args = array()) {
         echo __METHOD__ .' <br/>';
    }

    public function getOne($sql, array $args = array()) {
        //获取实参列表
        $args  = func_get_args();
        //获取第一个参数,是一个sql结构语句
        $sql   = array_shift($args);
        //获取字段关键信息,比如要查询的字段名和条件
        $parms = $args;
        //对sql结构语句进行拆分,形成一个数组
        $sqls  = preg_split('/\?[NTF]/', $sql);
        //由于最后一个是空字符串,比params多一个,所以删除掉
        array_pop($sqls);
        //创建变量,用于保存最终sql语句
        $sql   = '';
        //拼凑sql语句
        foreach ($sqls as $key => $value) {
            $sql .= $value . $parms[$key];
        }
         //执行获取结果集
        $rows = M()->query($sql);
        //获取一行结果
        if ($rows) {
            $row = array_shift($rows);
            return array_shift($row);
        }
    }

    public function getRow($sql, array $args = array()) {
         //获取实参列表
        $args = func_get_args();
        //获取第一个参数 是一个sl结构语句
        $sql = array_shift($args);
        //获取字段关键信息 
        $parms = $args;
        //对sql结构语句进行拆分 形成数组 所以删除一个
        $sqls =  preg_split( '/\?[NTF]/', $sql);
        //由于最后一个是空的字符串
        array_pop($sqls);
        //创建变量 用于保存最终sql
        $sql = '';
        //拼接sql语句
        foreach ($sqls as $key=>$value){
            $sql .=$value . $parms[$key];
        }
        //执行sql语句  获得结果
        $rows = M()->query($sql);
        if($rows){
            return array_shift($rows);
        }
    }

    public function insert($sql, array $args = array()) {
         //获取实参列表
        $args = func_get_args();
        //获得第一个参数 是一个sql结构语句
        $sql = array_shift($args);
        //获取字段关键信息,比如要查询的字段名和条件
        $table_name = $args[0];//字段名字
        $parms = $args[1];//条件
        //删除id
        unset($parms['id']);
        //对sql结构语句进行拆分,形成一个数组
        $sql = str_replace('?T','`'. $table_name . '`',$sql);  
        $fields = array();
        foreach ($parms as $key=>$value){
            $fields[] = '`'.$key.'`="'.$value.'"';
        }
        $field_str = implode(',', $fields);//把数组转换为字符串 就是update的值
        $sql = str_replace("?%", $field_str, $sql);
        if(M()->execute($sql)!==FALSE){
            return M()->getLastInsID();
        }  else {
            return false;    
        }
    }

    public function query($sql, array $args = array()) {
        //获取实参列表
        $args = func_get_args();
        //获取第一个参数 是一个sl结构语句
        $sql = array_shift($args);
        ////获取字段关键信息 
        $parms = $args;
        //对sql结构语句进行拆分 形成数组 所以删除一个
        $sqls = preg_split('/\?[TFN]/', $sql) ;
        //由于最后一个是空的字符串
        array_pop($sqls);
        //创建标量 用来保存最终sql语句
        $sql = '';
        foreach ($sqls as $key=>$value){
            //拼接sql语句
            $sql .= $value.$parms[$key];
        }
        return M()->execute($sql);
    }

    public function update($sql, array $args = array()) {
         echo __METHOD__ .' <br/>';
    }
}
