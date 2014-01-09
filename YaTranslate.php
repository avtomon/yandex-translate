<?php
    /**
     * Yandex Translate
     * This system is like Bing Translator or Google translate, but api is free for use and without strict limits.
     * The API offers text translation features for over 30 languages. 
     * 
     * To begin using this class you need to get your own API key, you can do it just in pair of clicks.
     * Firstly create a Yandex account - https://passport.yandex.com/passport
     * Finally get your API key - http://api.yandex.com/key/form.xml?service=trnsl
     * 
     * Official api description and documentation in English - http://api.yandex.com/translate/
     *                                            in russian - http://api.yandex.ru/translate/
     * 
     * Example:
     * 
     * include "YaTranslate.php";
     * $key = 'YOUR_API_KEY';
     * $tr  = new YaTranslate($key);
     * 
     * $result = $tr->detect('Γεια');                                  //Detects language of the original text
     * var_dump($result);
     * 
     * $result = $tr->translate('Hello, how are you?','en-fr');        //Translate from French to English
     * ($result);
     * 
     * $result = $tr->translate('Bonjour, comment allez-vous?','en');  //Detects language of the original text and translates in English
     * var_dump($result);
     * 
     * @author Tebiev Aleksandr | zzt.tzz@gmail.com
     **/
class YaTranslate{
    
    private $key                = '';
    private $request_url        = 'https://translate.yandex.net/api/v1.5/tr';
    private $request_timeout    = 4;
    /**
     * Request data method POST or GET
     * @var string
     */
    private $request_method     = 'POST';

    /**
     * Result format data, could be 'json', 'xml', and maybe in future 'array'
     * @var string
     */
    private $result_format      = 'json';
    private $method             = '';
    
    function __construct($key, $result_format='json'){
        $this->set_key($key);
        $this->set_result_format($result_format);
    }

    public function set_key($key){
        $this->key = $key;
    }
    
    public function get_key(){
        RETURN $this->key;
    }
    
    private function set_method($method){
        $this->method = $method;
    }
    
    private function get_method(){
        RETURN $this->method;
    }
    
    public function set_result_format($result_format){
        $result_format = strtolower($result_format);
        $this->result_format = $result_format == 'xml' ? '' : $result_format;
    }
    
    public function get_result_format(){
        RETURN $this->result_format;
    }
    
    
    public function set_request_url($request_url){
        $this->request_url = $request_url;
    }
    
    public function get_request_url(){
        $result = $this->request_url;
        $result.= ($this->get_result_format()==TRUE ? '.'.$this->get_result_format() : '');
        $result.= '/' . $this->get_method();
        RETURN $result;
    }
    
    public function set_request_method($request_method){
        $this->request_method = strtoupper($request_method) == 'GET' ? 'GET' : 'POST';
    }
    
    public function get_request_method(){
        RETURN $this->request_method;
    }
    
    function send_request($url,$params=FALSE){
        if ($params==TRUE AND $this->get_request_method() == 'GET') {
            if (is_array($params)) {
                $params = http_build_query($params);
            }      
            $url .= '?' . $params;
        }

        $defaults = array(
            CURLOPT_URL             => $url,
            CURLOPT_HEADER          => FALSE,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_FOLLOWLOCATION  => TRUE,
            CURLOPT_MAXREDIRS       => 3,
            CURLOPT_TIMEOUT         => $this->request_timeout
        );
        
        if ($this->get_request_method() == 'POST') {
            $defaults += array(
                    CURLOPT_CUSTOMREQUEST   => 'POST',
                    CURLOPT_POSTFIELDS      => $params,
                );
        }
       
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
        
        RETURN $this->send_request($this->get_request_url(),array('key' => $this->get_key()) + $params);
    }
    
    /**
     * Returns a list of translation directions supported by the service.
     * 
     * @param string $ui        - possible values are the language codes like  ru, en, uk, be...
     *                            if set, the language names are displayed in the language which code corresponding to this parameter.
     * @param string $callback  - javascript callback function name, used only with json format
     * @return string
     * 
     * Response Codes:
     * 200  - Operation completed successfully
     * 401  - Invalid API key.
     * 402  - This API key has been blocked.
     * 
     * list of supported languages - http://api.yandex.ru/translate/langs.xml
     * Description in English   - http://api.yandex.com/translate/doc/dg/reference/getLangs.xml
     * Description in russin    - http://api.yandex.ru/translate/doc/dg/reference/getLangs.xml
     **/ 
    public function get_langs($ui=FALSE,$callback=FALSE){
        RETURN $this->prepare_request('getLangs',array('ui' => $ui, 'callback' => $callback));
    }

    /**
     * Detects the language of the specified text.
     * 
     * @param string $text      - The text to detect the language for.
     *                            Restrictions:
     *                            Although the text parameter is an array (you can pass multiple text parameters),
     *                            only one language tag is returned for the entire text. To detect the language 
     *                            of each text item, call the detect method for each of them.
     * @param string $callback  - javascript callback function name, used only with json format
     * @return string
     * 
     * Response Codes:
     * 200  - Operation completed successfully
     * 401  - Invalid API key.
     * 402  - This API key has been blocked.
     * 403  - You have reached the daily limit for requests (including calls of the translate method).
     * 404  - You have reached the daily limit for the volume of translated text (including calls of the translate method).
     * 
     * Description in English   - http://api.yandex.com/translate/doc/dg/reference/detect.xml
     * Description in russin    - http://api.yandex.ru/translate/doc/dg/reference/detect.xml
     **/ 
    public function detect($text,$callback=FALSE){
        RETURN $this->prepare_request('detect',array('text' => $text, 'callback' => $callback));
    }
    
    /**
     * Translates text to the needed language
     * 
     * @param string $text      - Text which should be translated
     * @param string $lang      - Translation direction (for example, "en-ru" or "ru").
     *                            Format:
     *                            1.    A pair of language codes separated by a dash. 
     *                                  For example, "en-ru" specifies to translate from English to Russian.
     *                            2.    Single language code. 
     *                                  For example, "ru" specifies to translate to Russian. 
     *                                  In this case, the language of the original text is detected automatically.
     * @param string $format    - Text format.
     *                            Possible values:
     *                            "plain"   - Text without markup (default value).
     *                            "html"    - Text in HTML format.
     * @param integer $options  - Translation options.
     *                            Possible values:
     *                            1 - Automatically detect language. For example, if the lang parameter has reversed 
     *                            the translation direction for a pair, the service automatically detects the text 
     *                            language and returns it in the detected tag.
     * @param string $callback  - javascript callback function name, used only with json format
     * @return string
     * 
     * Response Codes:
     * 200  - Operation completed successfully
     * 401  - Invalid API key.
     * 402  - This API key has been blocked.
     * 403  - You have reached the daily limit for requests (including calls of the detect method).
     * 404  - You have reached the daily limit for the volume of translated text (including calls of the detect method).
     * 413  - The text size exceeds the maximum.
     * 422  - The text could not be translated. 
     * 501  - The specified translation direction is not supported.
     * 
     * Description in English   - http://api.yandex.com/translate/doc/dg/reference/translate.xml
     * Description in russin    - http://api.yandex.ru/translate/doc/dg/reference/translate.xml
     **/ 
    public function translate($text, $lang=FALSE, $format=FALSE, $options=FALSE, $callback=FALSE){
        RETURN $this->prepare_request('translate',array('text' => $text, 'lang' => $lang, 'format' => $format, 'options' => $options, 'callback' => $callback));
    }
}