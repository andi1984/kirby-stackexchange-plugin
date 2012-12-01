<?php
/**
 * Created by JetBrains PhpStorm.
 * Author: Andreas Sander
 * Creation: 01.12.12
 */
class stackexchange
{
    /*
     * Response is compressed by StackExchange (see https://api.stackexchange.com/docs/compression)
     */
    private $API_RESPONSE_COMPRESSION = 'compress.zlib';
    private $API_BASE_URL = 'https://api.stackexchange.com';
    private $API_VERSION = '2.1';
    private $token = null;

    /**
     * Constructor
     * @param string $_token: Your StackExchange API Token
     */
    function __construct($_token = '') {
        if(trim($_token) != '') {
            // Save token for class instance and later usage
            $this->token = $_token;
        }
    }

    /**
     * makeAPIRequest covers the whole request task
     * @param array $requestParams : This is an array containing all request params
     * (array key names are the original StackExchange API parameter names)
     * @param array $methodsArray:This $methodsArray is an non-empty array specifying which type of information (or which kind of action) the request should deliver/do
     * Each $methodsArray entry contains an array containing all further information about the method (and corresponding filters for this method)
     *      $methodsArray
     *      |
     *      -> $methodsArray[0] (mandatory)
     *      |
     *      -> $methodsArray[1]... (optional)
     *
     *      Each single method array contains at least a 'name' property and optional a method filter with key 'filter' corresponding to
     *      the keys ids, id, tags, tag etc. @ StackExchange API
     *
     *      Example:
     *
     *      http://api.stackexchange.com/docs/answers-by-ids
     *
     *      The method array for the above example would be: methodArray['name'] = 'answers' and
     *      optional methodArray['filter'] = '13659270;13659321' to filter answers with the ids specified in this semicolon separated list
     *
     *      You get an overview of existing methods @ http://api.stackexchange.com/docs/
     *
     *
     * @return string
     */
    public function makeAPIRequest($methodsArray, $requestParams = array()){
        //Start building the requestURL
        $requestURL = '';
        $requestURL .= $this->API_RESPONSE_COMPRESSION.'://';
        $requestURL .= $this->API_BASE_URL.'/';
        $requestURL .= $this->API_VERSION.'/';

        //Add methods to $requestURL
        if(!empty($methodsArray)){
            //Method Array is set and contains at least one item --> get the first (main) item/method
            $mainMethodObject = $methodsArray[0];
            $requestURL .= $this->addMethodToURL($mainMethodObject);

            //TODO: Support for second, third methods
        }

        //If API Token is set, add it to the request parameters
        if(isset($this->token)) {
            $requestParams['key'] = $this->token;
        }

        //Add params to $requestURL
        if(!empty($requestParams)){
           $requestURL .= $this->addParamsToURL($requestParams);
        }

        //Send request
        $data = file_get_contents($requestURL);
        if($data !== false){
            //Data received --> Return it json decoded
            return json_decode($data);
        } else {
            return new Exception('API Request failed!');
        }
    }

    /**
     * @param $methodArray: single method array (see makeAPIRequest definition)
     * @return string: string containing url encoded method parameters
     */
    protected function addMethodToURL($methodArray) {
        $requestURL = '';
        if(array_key_exists('name',$methodArray)){
            $mainMethodName = $methodArray['name'];
            //$mainMethod only contains letters
            if(preg_match('/[A-Za-z]/',$mainMethodName)) {
                $requestURL .= $mainMethodName;
                if(array_key_exists('filter',$methodArray)) {
                    $mainMethodFilterList = $methodArray['filter'];
                    $requestURL .= '/'.urlencode($mainMethodFilterList);
                    //TODO: Further check if filter list is valid (concerning API Logic, one ore more list entries allowed etc.)
                }
            }
        }

        return $requestURL;
    }

    /**
     * @param $paramsArray: array with all parameters
     * @return string containing url encoded paramaters
     */
    protected function addParamsToURL($paramsArray) {
        $requestURL = '';
        $paramNumber = 0;
        foreach ($paramsArray as $paramKey => $paramValue) {
            $paramNumber += 1;

            if($paramNumber == 1) {
                $requestURL .= '?';
            }else {
                $requestURL .= '&';
            }

            $requestURL .= urlencode($paramKey);
            $requestURL .= '=';
            $requestURL .= urlencode($paramValue);
        }

        return $requestURL;
    }
}
