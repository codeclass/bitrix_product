<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 10.10.2019
 * Time: 18:19
 */

namespace Codeclass\parser\lib\product\props;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class EnumHL extends Enum
{

    var $ENTITY_DATA_CLASS;


    public function __construct($IBLOCK_ID, $CODE, $TABLE, $IS_REQUIRED = false)
    {
        parent::__construct($IBLOCK_ID, $CODE, $IS_REQUIRED);
        Loader::includeModule("highloadblock");

        $hlblock = HL\HighloadBlockTable::getList(array(
            "select" => array("*"),
            "filter" => array("TABLE_NAME" => $TABLE)  // Задаем параметры фильтра выборки
        ))->fetch();

        if (!$hlblock)
            throw new \Exception('Unknown table ' . $TABLE);

        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $this->ENTITY_DATA_CLASS = $entity->getDataClass();
    }

    public function load()
    {
        $entity_data_class = $this->ENTITY_DATA_CLASS;
        $list_res = $entity_data_class::getList([
            "select" => array("*"),
        ]);
        while ($list_row = $list_res->Fetch()) {
            $this->VALUES[$list_row['ID']] = $list_row['UF_XML_ID'];
            $this->VALUES_XML[$list_row['UF_XML_ID']] = $list_row['UF_NAME'];
        }
    }


    protected function _getValue($id)
    {
        if (isset($this->VALUES_XML[$id])) {
            $ret = $this->VALUES_XML[$id];
        } else {
            if ($this->IS_REQUIRED)
                throw new \Exception('No value for id ' . $id);
            $ret = null;
        }
        return $ret;
    }


    protected function _getIDByVal($val)
    {
        if (!$key = array_search($val, $this->VALUES_XML))
            throw new \Exception('No such value ' . $val);
        return $key;
    }


}