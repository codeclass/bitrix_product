<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 18:40
 */

namespace Codeclass\parser\lib\product\props;


class PropListHL extends PropList {

    var $PROPERTY_TYPE = 'S';
    var $ENUM;

    public function __construct($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY = false)
    {
        parent::__construct($IBLOCK_ID, $CODE, $ELEMENT_ID, $ENTITY);

        $this->ENUM = new EnumHL($IBLOCK_ID, $CODE, $ENTITY['USER_TYPE_SETTINGS']['TABLE_NAME']);

    }

}