<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 17:42
 */

namespace Codeclass\parser\lib\product;

use Codeclass\parser\lib\product\props\Image;
use Codeclass\parser\lib\product\props\Prop;
use Codeclass\parser\lib\product\props\Section;

class BXElement {

    /*
     * Fields
     */

    protected $ID = null;

    protected $IBLOCK_ID;

    protected $FIELDS = array(
        'ACTIVE' => 'N',
        'SORT' => 500,
        'NAME' => '',
        'CODE' => '',
        'XML_ID' => '',
        'PREVIEW_TEXT' => '',
        'PREVIEW_TEXT_TYPE' => 'html',
        'DETAIL_TEXT' => '',
        'DETAIL_TEXT_TYPE' => 'html',
        'EXTERNAL_ID'
    );

    protected $PREVIEW_PICTURE = null;
    protected $DETAIL_PICTURE = null;

    protected $IBLOCK_SECTION_ID = null;

    protected $PROPERTIES = array();

    protected $changed = false;

    public static function findByFilter($IBLOCK_ID, $FILTER, $FIRST = false){
        $arFilter = array_merge(['IBLOCK_ID' => $IBLOCK_ID], $FILTER);
        $res  = \CIBlockElement::GetList([], $arFilter);
        $cnt = $res->SelectedRowsCount();
        if($cnt == 0)
            return [];

        $el = false;

        if($cnt > 1){
            if($FIRST){
                $el = $res->Fetch();
                return new BXElement($IBLOCK_ID, $el['ID']);
            } else {
                $ret = [];
                while($el = $res->Fetch()){
                    $ret[] = new BXElement($IBLOCK_ID, $el['ID']);
                }
                return $ret;
            }
        } elseif ($cnt == 1){
            $el = $res->Fetch();
            return new BXElement($IBLOCK_ID, $el['ID']);
        }

        return false;
    }

    public function __construct($IBLOCK_ID, $ELEMENT_ID = null)
    {
        $this->IBLOCK_ID = $IBLOCK_ID;
        $this->ID = $ELEMENT_ID;

        $this->PREVIEW_PICTURE = new Image();
        $this->DETAIL_PICTURE = new Image();
        $this->IBLOCK_SECTION_ID = new Section($this->IBLOCK_ID, $ELEMENT_ID);


        //Init Properties
        $props_res = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $IBLOCK_ID]);
        while($prop = $props_res->Fetch()){
            $this->PROPERTIES[$prop['CODE']] = Prop::getProp($this->IBLOCK_ID, $prop['CODE'], $this->ID, $prop);
        }
    }

    public function setID($ID){
        $this->ID = $ID;
        $this->IBLOCK_SECTION_ID->setElementID($ID);
        foreach ($this->PROPERTIES as $prop){
            $prop->setElementID($ID);
        }
    }

    public function load(){
        if(!$this->ID)
            throw new \Exception('ELEMENT_ID needed for load element');

        $el_res = \CIBlockElement::GetByID($this->ID);
        $el = $el_res->Fetch();
        if(!$el)
            throw new \Exception('Element not found ID: '. $this->ID);

        $this->FIELDS = $el;

        $this->PREVIEW_PICTURE->setID($el['PREVIEW_PICTURE']);
        $this->DETAIL_PICTURE->setID($el['DETAIL_PICTURE']);

        foreach($this->PROPERTIES as $prop){
            $prop->setElementID($this->ID);
        }

    }

    public function getField($CODE){
        if(!$this->changed && $this->ID)
            $this->load();

        $fields = array_keys($this->FIELDS);

        $special_fields = ['PREVIEW_PICTURE', 'DETAIL_PICTURE', 'IBLOCK_SECTION'];

        if(!in_array($CODE, array_merge($fields, $special_fields)))
            throw new \Exception('Field not found: ' . $CODE);

        $ret = false;

        if(in_array($CODE, $fields)){
            $ret = $this->FIELDS[$CODE];
        }

        if(in_array($CODE, $special_fields)){
            switch($CODE) {
                case 'PREVIEW_PICTURE':
                    $ret = $this->PREVIEW_PICTURE->getValue();
                    break;
                case 'DETAIL_PICTURE':
                    $ret = $this->DETAIL_PICTURE->getValue();
                    break;
                case 'IBLOCK_SECTION':
                    $ret = $this->IBLOCK_SECTION_ID->getValue();
                    break;
            }
        }
        return $ret;
    }

    public function setField($CODE, $VALUE){

        if(!$this->changed && $this->ID)
            $this->load();

        $fields = array_keys($this->FIELDS);

        $special_fields = ['PREVIEW_PICTURE', 'DETAIL_PICTURE', 'IBLOCK_SECTION'];

        if(!in_array($CODE, array_merge($fields, $special_fields)))
            throw new \Exception('Field not found: ' . $CODE);

        if(in_array($CODE, $fields)){
            $this->FIELDS[$CODE] = $VALUE;
        }

        if(in_array($CODE, $special_fields)){
            switch($CODE) {
                case 'PREVIEW_PICTURE':
                    $this->PREVIEW_PICTURE->setValue($VALUE);
                    break;
                case 'DETAIL_PICTURE':
                    $this->DETAIL_PICTURE->setValue($VALUE);
                    break;
                case 'IBLOCK_SECTION':
                    $this->IBLOCK_SECTION_ID->setValue($VALUE);
                    break;
            }
        }
        $this->changed = true;
    }

    public function getProperty($CODE){
        if(!$this->changed && $this->ID)
            $this->load();

        $props = array_keys($this->PROPERTIES);
        if(!in_array($CODE, $props))
            throw new \Exception('Property not found :' . $CODE);

        $ret = $this->PROPERTIES[$CODE]->getValue();

        return $ret;
    }

    public function setProperty($CODE, $VALUE){
        if(!$this->changed && $this->ID)
            $this->load();

        $props = array_keys($this->PROPERTIES);
        if(!in_array($CODE, $props))
            throw new \Exception('Property not found :' . $CODE);

        $ret = $this->PROPERTIES[$CODE]->setValue($VALUE);

        $this->changed = true;

        return $ret;
    }

    public function addPropertyEnum($CODE, $value, $xml_id= ''){
        if(!in_array($CODE, array_keys($this->PROPERTIES)))
            throw new \Exception('Property not found :' . $CODE);

        $this->PROPERTIES[$CODE]->addEnum($value, $xml_id);
    }

    public function addPropValue($CODE, $VALUE){
        if(!$this->changed && $this->ID)
            $this->load();

        $props = array_keys($this->PROPERTIES);
        if(!in_array($CODE, $props))
            throw new \Exception('Property not found :' . $CODE);

        $ret = $this->PROPERTIES[$CODE]->addValue($VALUE);

        $this->changed = true;

        return $ret;
    }

    public function setSection($SECTION){
        $this->IBLOCK_SECTION_ID->setValue($SECTION);
        $this->changed = true;
    }

    public function addSection($SECTION){
        $this->IBLOCK_SECTION_ID->addValue($SECTION);
        $this->changed=true;
    }

    public function removeSection($SECTION){
        $this->IBLOCK_SECTION_ID->removeValue($SECTION);
        $this->changed=true;
    }

    public function save(){
        if(!$this->changed)
            return;

        $ar = $this->_prepareFields();

        $el = new \CIBlockElement();

        if($this->ID){
            $res = $el->Update($this->ID, $ar);
            if(!$res){
                var_dump($res);
                var_dump(iconv('UTF-8', 'windows-1251', $el->LAST_ERROR));
                throw new \Exception('Error updating element');
            }

        } else {
            $res = $el->Add($ar);
            if(!$res){
                var_dump($res);
                var_dump(iconv('UTF-8', 'windows-1251', $el->LAST_ERROR));
                throw new \Exception('Error inserting element');
            }

            $this->setID($res);
        }
        //saving section
        $this->IBLOCK_SECTION_ID->save();
        //saving props
        foreach ($this->PROPERTIES as $prop){
            $prop->save();
        }
        $this->changed = false;
    }

    protected function _prepareFields(){
        $ar = $this->FIELDS;
        $ar['IBLOCK_ID'] = $this->IBLOCK_ID;
        $preview_picture = $this->PREVIEW_PICTURE->getForSave();
        $ar['PREVIEW_PICTURE'] = $preview_picture['VALUE'];
        $detail_picture = $this->DETAIL_PICTURE->getForSave();
        $ar['DETAIL_PICTURE'] = $detail_picture['VALUE'];

        $ar['IBLOCK_SECTION_ID'] = $this->IBLOCK_SECTION_ID->getForSave();
        return $ar;
    }



}