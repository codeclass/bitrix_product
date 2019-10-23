<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 11.10.2019
 * Time: 16:51
 */

namespace Codeclass\parser\lib\product\props;

class Section {

    var $IBLOCK_ID;

    var $ELEMENT_ID;

    var $SECTIONS;
    var $SECTIONS_TREE;

    var $VALUE = false;

    var $changed = false;

    public function __construct($IBLOCK_ID, $ELEMENT_ID=null)
    {
        $this->IBLOCK_ID = $IBLOCK_ID;
        $this->setElementID($ELEMENT_ID);
        $this->_loadSections();
    }

    public function setElementID($ELEMENT_ID){
        if($this->ELEMENT_ID && $this->ELEMENT_ID != $ELEMENT_ID)
            throw new \Exception('ELEMENT_ID already exists');

        $this->ELEMENT_ID = $ELEMENT_ID;
    }

    protected function _loadSections(){
        $res = \CIBlockSection::getList([], ['IBLOCK_ID' => $this->IBLOCK_ID]);
        $parents = [];
        while($section = $res->Fetch()){
            $this->SECTIONS[$section['ID']] = $section['NAME'];
            $parent = $section['IBLOCK_SECTION_ID'];
            if($parent === null) $parent=0;
            $this->SECTIONS_TREE[$section['ID']] = $parent;
        }
    }

    public function load(){
        if(!$this->ELEMENT_ID)
            throw new \Exception('ELEMENT_ID needed to load value');

        $this->VALUE = [];
        $connection = \Bitrix\Main\Application::getConnection();
        $sql = "SELECT * FROM b_iblock_section_element r WHERE r.IBLOCK_ELEMENT_ID = {$this->ELEMENT_ID}";
        $recordset = $connection->query($sql);
        while ($record = $recordset->fetch())
        {
            $this->VALUE[] = $record['IBLOCK_SECTION_ID'];
        }
        $this->changed = false;
    }

    public function save(){
        if(!$this->ELEMENT_ID)
            throw new \Exception('ELEMENT_ID needed for save');

        $connection = \Bitrix\Main\Application::getConnection();
        //Сначала все удалим
        $sql = "DELETE FROM b_iblock_section_element WHERE IBLOCK_ELEMENT_ID = {$this->ELEMENT_ID}";
        $res = $connection->queryExecute($sql);

        //var_dump($res);

        foreach ($this->VALUE as $section_id){
            $sql = "INSERT INTO b_iblock_section_element (IBLOCK_SECTION_ID, IBLOCK_ELEMENT_ID) VALUES ($section_id, {$this->ELEMENT_ID})";
            $connection->queryExecute($sql);
        }

        $this->changed = false;
    }

    public function getForSave(){
        if(!$this->changed)
            $this->load();

        if(empty($this->VALUE))
            return null;

        return $this->VALUE[0];
    }

    public function getValue(){
        if($this->VALUE === false)
            $this->load();

        $ret = [];
        foreach ($this->VALUE as $val){
            $ret[] = $this->SECTIONS[$val];
        }

        return $ret;
    }

    public function setValue($VALUE){
        $this->VALUE = [];
        if(is_array($VALUE)){
            foreach($VALUE as $val){
                $this->VALUE[] = $this->_getIdByValue($val);
            }
        } else {
            $this->VALUE[] = $this->_getIdByValue($VALUE);
        }
        $this->VALUE = array_unique($this->VALUE);
        $this->changed=true;
    }

    protected function _getIdByValue($val){
        $res = array_search($val, $this->SECTIONS);
        if(!$res)
            throw new \Exception('Section not found: ' . $val);
        return $res;
    }


    public function addValue($VALUE){
        if($this->VALUE === false)
            $this->load();
        $this->VALUE[] = $this->_getIdByValue($VALUE);
        $this->VALUE = array_unique($this->VALUE);
        $this->changed = true;
    }

    public function removeValue($VALUE){
        if($this->VALUE === false)
            $this->load();
        $to_remove = $this->_getIdByValue($VALUE);
        $new_val = [];
        foreach($this->VALUE as $val){
            if($val != $to_remove){
                $new_val[] = $val;
            }
        }
        $this->VALUE = $new_val;
        $this->changed = true;
    }

    public function isChanged(){
        return $this->changed;
    }
}