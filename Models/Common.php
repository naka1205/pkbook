<?php
namespace Models;
class Common
{
    public $data;
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : '';
    }
    public function __set($name,$value)
    {
        $this->data[$name] = $value;
    }
}