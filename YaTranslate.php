<?php
class YaTranslate{
    
    private $key = '';
    private $request_url = 'https://translate.yandex.net/api/v1.5/tr.json';
    private $request_timeout = 4;
    
    public function set_key($key){
        $this->key = $key;
    }
    
    public function get_key(){
        RETURN $this->key;
    }
    
    public function set_request_url($request_url){
        $this->request_url = $request_url;
    }
    
    public function get_request_url(){
        RETURN $this->key;
    }
    
    function __construct($key){
        $this->set_key($key);
    }
    
    function send_request(){
        $defaults = array(
            CURLOPT_URL             => $this->request_url,
            CURLOPT_HEADER          => FALSE,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_TIMEOUT         => $this->request_timeout
        );
       
        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        if ( ! $result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        RETURN $result; 
    }

}