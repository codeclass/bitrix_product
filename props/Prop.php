<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 18:26
 */

namespace Codeclass\parser\lib\product\props;


use PhpImap\Exception;

abstract class Prop {

    protected $PROPERTY_TYPE;

    /**
     * @var Иденитификатор значения свойства
     */
    protected $ID;

    protected $ELEMENT_ID = null;

    protected $VALUE = null;
    protected $VALUES = array();

    protected $ENTITY;

    protected $CHANGED = false;


    public function __construct($IBLOCK_ID, $CODE, $ELEMENT_ID = null, $ENTITY = false)
    {
        if(!$ENTITY){
            $entity_res = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $IBLOCK_ID , 'CODE' => $CODE]);
            $ENTITY = $entity_res->Fetch();
        }

        if(!$ENTITY)
            throw new \Exception('Property with code ' . $CODE . ' not found in IBLOCK_ID ' . $IBLOCK_ID);

        $this->ENTITY = $ENTITY;

        if($this->ENTITY['PROPERTY_TYPE'] != $this->PROPERTY_TYPE)
            throw new \Exception('Wrong property type');

        $this->ELEMENT_ID = $ELEMENT_ID;
    }

    abstract function load();

    abstract function setValue($VALUE);

    abstract function getValue();

    abstract function addValue($VALUE);

    abstract function save();

    public static function getProp($IBLOCK_ID, $CODE, $ELEMENT_ID = null, $ENTITY = false){

        if(!$ENTITY){
           $entity_res = \CIBlockProperty::GetList([], ['IBLOCK_ID' => $IBLOCK_ID , 'CODE' => $CODE]);
           $ENTITY = $entity_res->Fetch();
        }

        if(!$ENTITY)
            throw new \Exception('Property with code ' . $CODE . ' not found in IBLOCK_ID ' . $IBLOCK_ID);

        switch($ENTITY['PROPERTY_TYPE']){
            case 'S' :
                switch($ENTITY['USER_TYPE']){
                    case NULL :
                        $res = new PropString($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);
                        break;
                    case 'directory' :
                        $res = new PropListHL($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);
                        break;
                    default :
                        throw new \Exception('Unknown property USER_TYPE ' . $ENTITY['USER_TYPE']);
                }
                break;
            case 'N' : $res = new PropInt($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);
                break;
            case 'L' : $res = new PropList($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);
                break;
            case 'F' : $res = new PropFile($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);
                break;
            case 'E' : $res = new PropEList($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);
                break;
            default:
                throw new \Exception('Unknown property type ' . $ENTITY['PROPERTY_TYPE']);
        }

        return $res;
    }

    public function isMultiple(){
        return $this->ENTITY['MULTIPLE'] == 'Y';
    }

    public function setElementID($ELEMENT_ID){
        $this->ELEMENT_ID = $ELEMENT_ID;
    }

}