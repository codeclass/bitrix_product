<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 10.10.2019
 * Time: 18:19
 */

namespace Codeclass\parser\lib\product\props;

class Enum {

    var $IBLOCK_ID;
    var $CODE;

    var $VALUES = false;
    var $VALUES_XML = false;

    public function __construct($IBLOCK_ID, $CODE)
    {
        $this->IBLOCK_ID = $IBLOCK_ID;
        $this->CODE = $CODE;
    }

    public function load(){
        $list_res = \CIBlockPropertyEnum::GetList([], ['IBLOCK_ID' => $this->IBLOCK_ID, 'CODE' => $this->CODE]);
        while($list_row = $list_res->Fetch())
        {
            $this->VALUES[$list_row['ID']] = $list_row['VALUE'];
            $this->VALUES_XML[$list_row['ID']] = $list_row['XML_ID'];
        }
    }

    public function getValue($data){

        if($this->VALUES === false){
            $this->load();
        }
        if(is_array($data)){
            $ret = [];
            foreach ($data as $id){
                $ret[] = $this->_getValue($id);
            }
        } else {
            $ret = $this->_getValue($data);
        }
        return $ret;
    }

    protected function _getValue($id){
        if(isset($this->VALUES[$id])){
            $ret = $this->VALUES[$id];
        } else
            throw new \Exception('No value for id ' . $id);
        return $ret;
    }

    public function getIDbyVal($data){
        if($this->VALUES === false){
            $this->load();
        }
        if(is_array($data)){
            $ret = [];
            foreach ($data as $val){
                $ret[] = $this->_getIDByVal($val);
            }
        } else {
            $ret = $this->_getIDByVal($data);
        }
        return $ret;
    }

    protected function _getIDByVal($val){
        if(!$key = array_search($val, $this->VALUES))
            throw new \Exception('No such value ' . $val);
        return $key;
    }

    public function getIDbyXML($data){

    }

    public function getXML($data){

    }
}