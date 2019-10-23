<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 12.10.2019
 * Time: 17:39
 */

namespace Codeclass\parser\lib\product\props;

class Product {

    var $ID;

    var $FIELDS = [
        'QUANTITY' => 0,
        'WEIGHT' => 0,
        'WIDTH' => 0,
        'LENGTH' => 0,
        'HEIGHT' => 0,
        'MEASURE' => 5,  //шт
        'PRICE_TYPE' => 'S',
        'PURCHASING_PRICE' => 0,
        'PURCHASING_CURRENCY' => 'RUB',
        'TYPE' => 1 //простой товар
    ];

    const PROD_SIMPLE = 1;
    const PROD_COMPLECT = 2;
    const PROD_WITH_OFFERS = 3;
    const PROD_OFFER = 4;

    var $changed = false;

    public function __construct($ELEMENT_ID = null)
    {
        $this->setElementID($ELEMENT_ID);
    }

    public function setElementID($ELEMENT_ID){
        if($this->ID && $this->ID != $ELEMENT_ID)
            throw new \Exception('ELEMENT_ID already exists');

        $this->ID = $ELEMENT_ID;
    }

    public function load(){
        if(!$this->ID)
            throw new \Exception('ELEMENT_ID needed to load value');

        $row = \CCatalogProduct::GetByID($this->ID);

        //var_dump($row);

        if($row) {
            foreach($row as $k => $v){
                if(isset($this->FIELDS[$k]))
                    $this->FIELDS[$k] = $v;
            }
            $this->FIELDS['ID'] = $row['ID'];
            //$this->FIELDS = $row;
            $this->changed = false;
        }
    }

    public function save(){
        if(!$this->changed)
            return;

        if(!$this->ID)
            throw new \Exception('ELEMENT_ID needed to save product');

        $pr = new \CCatalogProduct();

        if(!isset($this->FIELDS['ID'])){
            $ar = $this->FIELDS;
            $ar['ID'] = $this->ID;

            $res = $pr->Add($ar);

            if(!$res)
                throw new \Exception('Product add error');

        } else {
            $ar = $this->FIELDS;
            unset($ar['ID']);
            $res = $pr->Update($this->ID, $ar);
            if(!$res) {
                throw new \Exception('Product update error');
            }
        }
        $this->changed = false;
    }


    public function getValue($CODE){
        if(!$this->changed && !isset($this->FIELDS['ID'])){
            $this->load();
        }
        if(!in_array($CODE, array_keys($this->FIELDS)))
            throw new \Exception('CODE not found');

        return $this->FIELDS[$CODE];
    }

    public function setValue($CODE, $VALUE){
        if(!in_array($CODE, array_keys($this->FIELDS)))
            throw new \Exception('CODE not found: ' . $CODE);

        $this->FIELDS[$CODE] = $VALUE;
        $this->changed = true;
    }




}