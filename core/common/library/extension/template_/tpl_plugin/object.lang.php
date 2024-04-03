<?php

/* TEMPLATE PLUGIN OBJECT EXAMPLE */

include_once($_SERVER["DOCUMENT_ROOT"] . "/model/global/language.class.php");
class tpl_object_lang extends language
{
    public function __construct()
    {
        parent::__construct();
    }
}