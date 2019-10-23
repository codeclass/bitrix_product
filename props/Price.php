<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 12.10.2019
 * Time: 20:09
 */

namespace Codeclass\parser\lib\product\props;

class Price {

    var $ELEMENT_ID;

    var $PRICES_TYPE;
    var $BASE_PRICE;

    var $PRICES;

    var $changed = false;

    public function __construct($ELEMENT_ID = null)
    {
        $this->setElementID($ELEMENT_ID);
        $this->getPricesTypes();
    }

    public function setElementID($ELEMENT_ID){
        if($this->ELEMENT_ID && $this->ELEMENT_ID != $ELEMENT_ID)
            throw new \Exception('ELEMENT_ID already exists');

        $this->ELEMENT_ID = $ELEMENT_ID;
    }

    protected function getPricesTypes(){
        $this->PRICES_TYPE = [];
        $db_prices=\CCatalogGroup::GetList(array(), array());
        while($ar_price=$db_prices->GetNext()){
            $this->PRICES_TYPE[$ar_price['ID']]=array(
                'ID' => $ar_price['ID'],
                'NAME' => $ar_price['NAME'],
                'BASE' => $ar_price['BASE'],
                'NAME_LANG' => $ar_price['NAME_LANG'],
                'CODE' => 'PRICE_'.$ar_price['ID'],
                'MULTIPLE' => 'N',
                'PROPERTY_TYPE' => 'T'
            );
            if($ar_price['BASE'] == 'Y')
                $this->BASE_PRICE = $ar_price['ID'];
        }
        var_dump($this->PRICES);
    }

    public function load(){
        if(!$this->ELEMENT_ID)
            throw new \Exception('ELEMENT_ID needed to load value');

        $db_res = \CPrice::GetList([], ['PRODUCT_ID' => $this->ELEMENT_ID]);

        $prices = [];

        while($row = $db_res->Fetch()){
            $prices[$row['CATALOG_GROUP_ID']] = $row;
        }

        $this->PRICES = [];

        foreach($this->PRICES_TYPE as $price_type){
            $price_type_id = $price_type['ID'];
            if(isset($prices[$price_type_id])){
                $this->PRICES[$price_type_id] = $prices[$price_type_id];
            }
        }

        $this->changed = false;
    }

    public function setPrice($CATALOG_GROUP, $PRICE, $CURRENCY  = 'RUB'){
        if(!in_array($CATALOG_GROUP, array_keys($this->PRICES_TYPE)))
            throw new \Exception('Unknown price type');

        $this->PRICES[$CATALOG_GROUP]['PRICE'] = $PRICE;
        $this->PRICES[$CATALOG_GROUP]['CURRENCY'] = $CURRENCY;
        $this->changed = true;
    }

    public function setBasePrice($PRICE, $CURRENCY = 'RUB')
    {
        $this->setPrice($this->BASE_PRICE, $PRICE, $CURRENCY);
    }

    public function getPrice($CATALOG_GROUP)
    {
        if(!$this->changed)
            $this->load();

        $ret = 0;
        if(isset($this->PRICES[$CATALOG_GROUP])){
            $ret = $this->PRICES[$CATALOG_GROUP]['PRICE'];
        }
        return $ret;
    }

    public function getBasePrice(){
        $this->getPrice($this->BASE_PRICE);
    }

    public function save(){
        if(!$this->changed)
            return;

        if(!$this->ELEMENT_ID)
            throw new \Exception('ELEMENT_ID needed to save price');

        foreach ($this->PRICES as $catalog_group_id => $price){

            $res = \CPrice::GetList(array(), array(
                "PRODUCT_ID" => $this->ELEMENT_ID,
                "CATALOG_GROUP_ID" => $catalog_group_id
            ));
            $arLoadPriceArray = array(
                "PRODUCT_ID" => $this->ELEMENT_ID,
                "CATALOG_GROUP_ID" => $catalog_group_id,
                "PRICE" => $price['PRICE'],
                "CURRENCY" => $price['CURRENCY']
            );
            if ($arr = $res->Fetch()) {
                $ret = \CPrice::Update($arr['ID'], $arLoadPriceArray);
            } else {
                $ret = \CPrice::Add($arLoadPriceArray);
            }

            if(!$ret)
                throw new \Exception('Error updating price');

        }


    }


}