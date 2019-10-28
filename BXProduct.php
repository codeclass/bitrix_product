<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 10.03.2019
 * Time: 17:58
 */

namespace Codeclass\parser\lib\product;


use Codeclass\parser\lib\product\props\Price;
use Codeclass\parser\lib\product\props\Product;

class BXProduct extends BXElement {

    var $PRODUCT_DATA;
    var $PRICE_DATA;

    var $OFFERS;

    var $IBLOCK_DATA;
    var $OFFERS_IBLOCK_DATA;

    public static function findByFilter($IBLOCK_ID, $FILTER){
        $arFilter = array_merge(['IBLOCK_ID' => $IBLOCK_ID], $FILTER);
        $res  = \CIBlockElement::GetList([], $arFilter);
        $ret = [];
        while($el = $res->Fetch()){
            $ret[] = new BXProduct($IBLOCK_ID, $el['ID']);
        }
        return $ret;
    }

    public function __construct($IBLOCK_ID, $ELEMENT_ID = null)
    {
        parent::__construct($IBLOCK_ID, $ELEMENT_ID);
        //Get Iblock Data
        $res = \CCatalog::GetByID($IBLOCK_ID);
        $this->IBLOCK_DATA = $res;

        $res = \CCatalog::GetList([], ['PRODUCT_IBLOCK_ID' => $IBLOCK_ID]);
        $this->OFFERS_IBLOCK_DATA = $res -> Fetch();

        //var_dump($this->OFFERS_IBLOCK_DATA);

        if($this->isCatalog()) {
            //Init product data
            $this->PRODUCT_DATA = new Product($ELEMENT_ID);
            //Init Price data
            $this->PRICE_DATA = new Price($ELEMENT_ID);
        }
        //Init Offers
        if($this->hasOffers() && $ELEMENT_ID) {
            $this->OFFERS = BXProduct::findByFilter($this->offersIBlock(), ['PROPERTY_CML2_LINK' => $ELEMENT_ID]);
        }
    }

    public function isCatalog(){
        return isset($this->IBLOCK_DATA['IBLOCK_ID']);
    }

    public function hasOffers(){
        return isset($this->OFFERS_IBLOCK_DATA['IBLOCK_ID']);
    }

    public function offersIBlock(){
        return $this->OFFERS_IBLOCK_DATA['IBLOCK_ID'];
    }

    public function setID($ID)
    {
        parent::setID($ID);

        if($this->isCatalog()) {
            $this->PRODUCT_DATA->setElementID($ID);
            $this->PRICE_DATA->setElementID($ID);
        }
        if($this->hasOffers()){
            foreach ($this->OFFERS as $offer){
                $offer->setProperty('CML2_LINK', $ID);
            }
        }
    }

    public function save()
    {
        $this->setTypeOffers();

        parent::save();
        if($this->isCatalog()) {
            $this->PRODUCT_DATA->save();
            $this->PRICE_DATA->save();
        }
        if($this->hasOffers()){
            foreach ($this->OFFERS as $offer){
                $offer->setProperty('CML2_LINK', $this->ID);
                $offer->save();
            }
        }
    }

    public function setProdValue($CODE, $VALUE){
        if($this->isCatalog())
            $this->PRODUCT_DATA->setValue($CODE, $VALUE);
    }

    public function getProdValue($CODE){
        if(!$this->isCatalog())
            return false;
        return $this->PRODUCT_DATA->getValue($CODE);
    }

    public function setPurchasingPrice($PRICE){
        if($this->isCatalog()) {
            $this->PRODUCT_DATA->setValue('PURCHASING_PRICE', $PRICE);
            $this->PRODUCT_DATA->setValue('PURCHASING_CURRENCY', 'RUB');
        }
    }

    public function setQuantity($QUANTITY){
        if($this->isCatalog()) {
            $this->PRODUCT_DATA->setValue('QUANTITY', $QUANTITY);
        }
    }

    public function setTypeOffers(){
        if($this->isCatalog() && $this->hasOffers() && count($this->OFFERS) > 0) {
            $this->PRODUCT_DATA->setValue('TYPE', 3);
        }
    }

    public function getQuantity(){
        if(!$this->isCatalog())
            return false;
        return $this->PRODUCT_DATA->getValue('QUANTITY');
    }

    public function setPrice($CATALOG_GROUP_ID, $PRICE, $CURRENCY = 'RUB')
    {
        if($this->isCatalog()) {
            $this->PRICE_DATA->setPrice($CATALOG_GROUP_ID, $PRICE, $CURRENCY);
        }
    }

    public function getPrice($CATALOG_GROUP_ID){
        if(!$this->isCatalog())
            return false;
        return $this->PRICE_DATA->getPrice($CATALOG_GROUP_ID);
    }

    public function setBasePrice($PRICE)
    {
        if($this->isCatalog()) {
            $this->PRICE_DATA->setBasePrice($PRICE);
        }
    }

    public function getBasePrice()
    {
        if(!$this->isCatalog())
            return false;
        return $this->PRICE_DATA->getBasePrice();
    }

    public function getOffers(){
        return $this->OFFERS;
    }

    public function findOfferByField($CODE, $VALUE)
    {
        if(!$this->hasOffers())
            return false;
        $ret = false;
        foreach ($this->OFFERS as $offer){
            $v = $offer->getField($CODE);
            if($v == $VALUE)
                $ret = $offer;
        }
        return $ret;
    }

    public function findOfferByProperty($CODE, $VALUE)
    {
        if(!$this->hasOffers())
            return false;
        $ret = false;
        foreach ($this->OFFERS as $offer){
            $v = $offer->getProperty($CODE);
            if($v == $VALUE)
                $ret = $offer;
        }
        return $ret;
    }

    public function addOffer(){
        $offer = new BXProduct($this->offersIBlock());
        $offer->setProdValue('TYPE', Product::PROD_OFFER);
        $this->OFFERS[] = $offer;
        return $offer;
    }

}