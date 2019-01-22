<?php
namespace Models;
class Page extends Common
{

    public function __construct($_id)
    {
        $data = self::data($_id);
        $this->data = $data;
    }


}