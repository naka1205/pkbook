<?php
namespace Extend;
use Exception;
class Config
{
    public static $configs = [];
    public static $file = ROOT_PATH . DS . "configs.php";
    // public static $file = ROOT_PATH . DS . "debug.php";
	public static function __callstatic($method, $arguments) {

    }

    public static function all(){
        if ( empty(self::$configs) ) {
            self::$configs = self::load();
        }
        return self::$configs;
    }

    public static function get($name = ''){
        if ( empty(self::$configs) ) {
            self::$configs = self::load();
        }
        return !empty($name) && isset(self::$configs[$name]) ? self::$configs[$name] : '';
    }

    public static function set($name,$value,$bool = false){
        if ( empty(self::$configs) ) {
            self::$configs = self::load();
        }
        self::$configs[$name] = $value;
        if ( $bool == true ) {
            return self::save();
        }
        return true;
    }

    public static function load(){
        self::$configs = require self::$file;
        return self::$configs;
    }

    public static function save(){
        if ( empty(self::$configs) ) {
            return false;
        }
        $configs = var_export(self::$configs,true);
        $configs = str_replace ('array (',' [ ',$configs);
        $configs = str_replace ('),',' ], ',$configs);
        return file_put_contents(self::$file,'<?php return ' . $configs . ';');
    }

}