<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 18:40
 */

namespace Codeclass\parser\lib\product\props;


class PropList extends Prop {

    var $PROPERTY_TYPE = 'L';
    var $ENUM;

    public function __construct($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY = false)
    {
        parent::__construct($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);

        $this->ENUM = new Enum($IBLOCK_ID, $CODE);

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

    public function addEnum($value, $xml_id = ''){
        $this->ENUM->addEnumValue($value, $xml_id);
    }

    protected function _loadMultiple($value_res){
        $this->VALUE = [];
        while($value = $value_res->Fetch())
            $this->VALUE[] = $value['VALUE'];
    }

    function setValue($VALUE){
        $this->CHANGED = true;
        $VALUE = $this->ENUM->getIDbyVal($VALUE);
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
        return $this->ENUM->getValue($this->VALUE);
    }

    function addValue($VALUE)
    {
        $this->getValue();

        if($this->isMultiple()){
            $this->_addValueMultiple($VALUE);
        } else
            throw new \Exception('Cannot add value because list field no multiple');

        $this->CHANGED = true;
    }

    protected function _addValueMultiple($VALUE){
        $VALUE = $this->ENUM->getIDbyVal($VALUE);
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