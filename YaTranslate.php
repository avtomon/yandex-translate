<?php
class YaTranslate{
    
    private $key                = '';
    private $request_url        = 'https://translate.yandex.net/api/v1.5/tr';
    private $request_timeout    = 4;
    private $result_format      = '';
    private $method             = '';

    public function set_key($key){
        $this->key = $key;
    }
    
    public function get_key(){
        RETURN $this->key;
    }
    
    public function set_method($method){
        $this->method = $method;
    }
    
    public function get_method(){
        RETURN $this->method;
    }
    
    public function set_result_format($result_format){
        $this->result_format = $result_format == 'xml' ? '' : $result_format;
    }
    
    public function get_result_format(){
        RETURN $this->result_format;
    }
    
    
    public function set_request_url($request_url){
        $this->request_url = $request_url;
    }
    
    public function get_request_url($params){
        // Assemble url this way
        
        //RETURN  $this->request_url . ($this->get_result_format()==TRUE ? '.'.$this->get_result_format() : '') .
        //        '/' . $this->get_method() . '?' . http_build_query( array('key' => $this->get_key()) + $params ) ;
                
        //Or this way
        $result = $this->request_url;
        $result.= ($this->get_result_format()==TRUE ? '.'.$this->get_result_format() : '');
        $result.= '/' . $this->get_method();
        $result.= '?' . http_build_query( array('key' => $this->get_key()) + $params );
        RETURN $result;
        
        //Which one is better?
    }
    
    function __construct($key, $result_format='json'){
        $this->set_key($key);
        $this->set_result_format($result_format);
    }
    
    function send_request($url){
        $defaults = array(
            CURLOPT_URL             => $url,
            CURLOPT_HEADER          => FALSE,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_FOLLOWLOCATION  => TRUE,
            CURLOPT_MAXREDIRS       => 3,
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
    
    public function prepare_request($method, $params = array()){
        foreach ($params as $key => $param){
            if ($param==FALSE) {
                unset($params[$key]);
            }
        }
        
        $this->set_method($method);
        
        $url = $this->get_request_url($params);
        RETURN $this->send_request($url);
    }
    
    public function get_langs($ui=FALSE,$callback=FALSE){
        RETURN $this->prepare_request('getLangs',array('ui' => $ui, 'callback' => $callback));
    }

}