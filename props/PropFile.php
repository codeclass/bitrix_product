<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 18:40
 */

namespace Codeclass\parser\lib\product\props;


class PropFile extends PropString {

    var $PROPERTY_TYPE = 'F';

    function load(){
        if(!$this->ELEMENT_ID)
            throw new \Exception('To load value ELEMENT_ID needed');

        $value_res = \CIBlockElement::GetProperty($this->ENTITY['IBLOCK_ID'], $this->ELEMENT_ID, [], ['CODE' => $this->ENTITY['CODE']]);

        if($this->isMultiple()){
            $this->_loadMultiple($value_res);
        } else {
            $value = $value_res->Fetch();
            $this->VALUE = new Image($value['VALUE']);
        }
        return $this;
    }

    protected function _loadMultiple($value_res){
        $this->VALUE = [];
        while($value = $value_res->Fetch())
            $this->VALUE[] = new Image($value['VALUE']);
    }


    function setValue($VALUE){
        $this->CHANGED = true;
        if($this->isMultiple()) {
            $this->_setValueMultiple($VALUE);
        } else {
            if(is_array($VALUE))
                throw new \Exception('Array value but type is not multiple');
            $this->VALUE = $this->_setValue($VALUE);
        }
    }

    protected function _setValueMultiple($VALUE){
        if(is_array($VALUE)){
            $this->VALUE = [];
            foreach ($VALUE as $val){
                $this->VALUE[] = $this->_setValue($val);
            }
        } else {
            $this->VALUE = [$this->_setValue($VALUE)];
        }
    }

    protected function _setValue($VALUE){
        //$VALUE - is link to file in docroot
        $ret = new Image();
        $ret->setValue($VALUE);
        return $ret;
    }

    function getValue(){
        if(!$this->CHANGED){
            $this->load();
        }
        if($this->isMultiple()){
            if(is_array($this->VALUE)){
                $ret = [];
                foreach ($this->VALUE as $val){
                    $ret[] = $this->_getValue($val);
                }
            } else {
                $ret = $this->_getValue($this->VALUE);
            }
        } else {
            $ret = $this->_getValue($this->VALUE);
        }

        return $ret;
    }

    protected function _getValue($file){
        return $file->getValue();
    }

    function addValue($VALUE)
    {
        $this->getValue();

        if($this->isMultiple()){
            $this->_addValueMultiple($VALUE);
        } else
            throw new \Exception('Array value but type is not multiple');

        $this->CHANGED = true;
    }

    protected function _addValueMultiple($VALUE){

        //@TODO fix add image not working
        if(is_array($VALUE)){
            $TO_ADD = [];
            foreach ($VALUE as $val){
                $TO_ADD[] = $this->_setValue($val);
            }
            $this->VALUE = array_merge($this->VALUE, $TO_ADD);
        } else {
            $this->VALUE[] = $this->_setValue($VALUE);
        }

    }

    function save(){
        if(!$this->CHANGED)
            return;

        if(!$this->ELEMENT_ID)
            throw new \Exception('To save value ELEMENT_ID needed');

        if($this->isMultiple()){
            $TO_SAVE = [];
            foreach($this->VALUE as $file){
                $TO_SAVE[] = $file->getForSave();
            }
        } else {
            $TO_SAVE = $this->VALUE->getForSave();
        }

        if($this->isMultiple() && empty($TO_SAVE)){
            $TO_SAVE=false; //Если необходимо сохранить пустое значение для множественного свойства, то используйте значение false, так как просто пустой массив не сохранится.
        }

        \CIBlockElement::SetPropertyValuesEx($this->ELEMENT_ID, $this->ENTITY['IBLOCK_ID'], array($this->ENTITY['CODE'] => $TO_SAVE));
        $this->CHANGED = false;
    }


}