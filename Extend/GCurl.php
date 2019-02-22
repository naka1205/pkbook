<?php
namespace Extend;

class GCurl{

    public $url;
    public $token;
    public function __construct(){
        $this->url = '';
        $this->token = '';
    }

    public function combine($data){
        $valueArr = array();
        foreach($data as $key => $val){
            $valueArr[] = "$key=$val";
        }
        $keys = implode("&",$valueArr);
        $this->url ."?";
        $this->url .= ($keys);
    }

    public function get($data = []){

        if ( !empty($data) ) {
            $this->combine($data);
        }

        $header[] = "Accept: application/json; charset=utf-8";
        $header[] = "User-Agent: Awesome-Octocat-App";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        $ret =  curl_exec($ch);
        curl_close($ch);

        return $ret;
    }

    public function post($data = ''){
        $header[] = "Accept: application/json; charset=utf-8";
        $header[] = "User-Agent: Awesome-Octocat-App";

        if ( !empty($this->token) ) {
            $header[] = "Authorization: token " . $this->token;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        if ( !empty($data) ) {
            $json = is_array($data) ? json_encode($data) : $data;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        }
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
}
