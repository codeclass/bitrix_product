<?php
/**
 * Created by PhpStorm.
 * User: Den
 * Date: 11.10.2019
 * Time: 11:18
 */

namespace Codeclass\parser\lib\product\props;

class Image {

    var $ID = null;
    var $OLD_ID = false;

    var $OBJ = false;
    var $OBJ_SAVE = false;

    var $changed = false;

    public function __construct($ID = null)
    {
        $this->setID($ID);
    }

    public function setID($ID){
        if($this->ID && $this->ID != $ID)
            throw new \Exception('ID already exists');
        $this->ID = $ID;
    }

    public function load()
    {
        if(!$this->ID) {
            //throw new \Exception('To load image ID needed');
            return false;
        }

        $rsFile = \CFile::GetByID($this->ID);
        $this->OBJ = $rsFile->Fetch();

        if(!$this->OBJ)
            throw new \Exception('Error image loading');

    }

    public function getValue(){
        $ret = NULL;

        if(!$this->OBJ && !$this->changed){
            $this->load();
        }

        if($this->ID)
            $ret = \Cfile::getPath($this->ID);

        if($this->OBJ_SAVE)
            $ret = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->OBJ_SAVE['tmp_name']);

        return $ret;
    }

    public function setValue($file){
        if($this->ID)
            $this->load();

        $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
        $this->OBJ_SAVE = \CFile::MakeFileArray($full_path);
        if(!$this->OBJ_SAVE)
            throw new \Exception('Error creating file: ' . $full_path);

        $this->changed = true;

        if($this->ID) {
            $this->OLD_ID = $this->ID;
            $this->ID = null;
            $this->OBJ_SAVE["old_file"] = $this->OLD_ID;
        }

        return true;
    }

    public function getForSave(){
        $this->getValue();

        if($this->changed)
            $ret = ['VALUE' => $this->OBJ_SAVE];

        if($this->ID)
            $ret = ['ID' => $this->ID];

        return $ret;
    }



}