<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 09.10.2019
 * Time: 18:40
 */

namespace Codeclass\parser\lib\product\props;


class PropInt extends PropString {

    var $PROPERTY_TYPE = 'N';

    function addValue($VALUE)
    {
        $this->getValue();

        if($this->isMultiple()){
            $this->_addValueMultiple($VALUE);
        } else {
            if(is_array($VALUE))
                throw new \Exception('Array value but type is not multiple');

            $this->VALUE = intval($this->VALUE) + intval($VALUE);
        }

        $this->CHANGED = true;
    }

}