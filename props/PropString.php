<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 18:40
 */

namespace Codeclass\parser\lib\product\props;


class PropString extends Prop {

    var $PROPERTY_TYPE = 'S';

    public function __construct($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY = false)
    {
        parent::__construct($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);

        if(!empty($this->ENTITY['DEFAULT_VALUE']))
            $this->VALUE = $this->ENTITY['DEFAULT_VALUE'];
    }

    function load(){
        if(!$this->ELEMENT_ID)
            throw new \Exception('To load value ELEMENT_ID needed');

        $value_res = \CIBlockElement::GetProperty($this->ENTITY['IBLOCK_ID'], $this->ELEMENT_ID, [], ['CODE' => $this->ENTITY['CODE']]);

        if($this->isMultiple()){
            $this->_loadMultiple($value_res);
        } else {
            $value = $value_res->Fetch();
            $this->VALUE = $value['VALUE'];
        }
        return $this;
    }

    protected function _loadMultiple($value_res){
        $this->VALUE = [];
        while($value = $value_res->Fetch())
            $this->VALUE[] = $value['VALUE'];
    }

    function setValue($VALUE){
        $this->CHANGED = true;
        if($this->isMultiple()) {
            $this->_setValueMultiple($VALUE);
        } else {
            if(is_array($VALUE))
                throw new \Exception('Array value but type is not multiple');
            $this->VALUE = $VALUE;
        }
    }

    protected function _setValueMultiple($VALUE){
        if(is_array($VALUE)){
            $this->VALUE = $VALUE;
        } else {
            $this->VALUE = [$VALUE];
        }
    }

    function getValue(){
        if(!$this->CHANGED){
            $this->load();
        }
        return $this->VALUE;
    }

    function addValue($VALUE)
    {
        $this->getValue();

        if($this->isMultiple()){
            $this->_addValueMultiple($VALUE);
        } else {
            if(is_array($VALUE))
                throw new \Exception('Array value but type is not multiple');

            $this->VALUE .= $VALUE;
        }

        $this->CHANGED = true;
    }

    protected function _addValueMultiple($VALUE){
        if(is_array($VALUE)){
            $this->VALUE = array_merge($this->VALUE, $VALUE);
        } else {
            $this->VALUE[] = $VALUE;
        }
    }

    function save(){
        if(!$this->CHANGED)
            return;

        if(!$this->ELEMENT_ID)
            throw new \Exception('To save value ELEMENT_ID needed');

        if($this->isMultiple() && empty($this->VALUE)){
            $this->VALUE=false; //Если необходимо сохранить пустое значение для множественного свойства, то используйте значение false, так как просто пустой массив не сохранится.
        }
        \CIBlockElement::SetPropertyValuesEx($this->ELEMENT_ID, $this->ENTITY['IBLOCK_ID'], array($this->ENTITY['CODE'] => $this->VALUE));
        $this->CHANGED = false;
    }

}