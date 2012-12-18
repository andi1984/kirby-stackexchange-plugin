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

    /*
     * Custom Methods
     */

	/* Answer Methods */
    /**
     * getAllAnswers simply gives you all answers on a specific site specified with site parameter in requestParams.
     * @param $requestParams: The general request parameters
     * @return Exception|string
     */
    public function getAllAnswers($requestParams) {
        return $this->getAnswers('all','',$requestParams);
    }

    /**
     * getAnswersByIDs returns all answers objects specified by the IDs contained in the semicolon separated string $ids
     * @param $ids: Semicolon separated string with all answer IDs you want to filter
     * @param $requestParams: The general request parameters
     * @return Exception|string
     */
    public function getAnswersByIDs($ids,$requestParams){
        return $this->getAnswers('selection',$ids,$requestParams);
    }

    /**
     * getCommentsForAnswersWithIDs returns all comments for answers specified by IDs in the $ids parameter
     * @param $ids: Semicolon separated string with all answer IDs
     * @param $requestParams: The general request parameters
     * @return Exception|string
     */
    public function getCommentsForAnswersWithIDs($ids,$requestParams){
        return $this->getAnswers('comments',$ids,$requestParams);
    }

	/* Question Methods */
	/**
	 * Gets all the questions on the site.
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getAllQuestions($requestParams) {
		return $this->getQuestions('all','',$requestParams);
	}

	/**
	 * Returns all the questions with active bounties in the system.
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getFeaturedQuestions($requestParams){
		return $this->getQuestions('featured','',$requestParams);
	}

	/**
	 * Returns questions the site considers to be unanswered.
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getUnansweredQuestions($requestParams){
		return $this->getQuestions('unanswered','',$requestParams);
	}

	/**
	 * Returns questions which have received no answers.
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getQuestionsWithNoAnswers($requestParams){
		return $this->getQuestions('no-answers','',$requestParams);
	}

	/**
	 * Returns the questions identified in $ids
	 * @param $ids: Semicolon separated string with all question IDs you want to filter
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getQuestionsByIDs($ids,$requestParams) {
		return $this->getQuestions('selection',$ids,$requestParams);
	}

	/**
	 * Gets the answers to a set of questions identified in $ids.
	 * @param $ids: Semicolon separated string with all question IDs you want to filter
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getAnswersForQuestionsWithIDs($ids,$requestParams){
		return $this->getQuestions('answers',$ids,$requestParams);
	}

	/**
	 * Gets the comments on question identified in $ids.
	 * @param $ids: Semicolon separated string with all question IDs you want to filter
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getCommentsForQuestionsWithIDs($ids,$requestParams){
		return $this->getQuestions('comments',$ids,$requestParams);
	}

	/**
	 * Get the questions that link to the questions identified by a set of $ids.
	 * @param $ids: Semicolon separated string with all question IDs you want to filter
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getLinkedQuestionsForQuestionsWithIDs($ids,$requestParams){
		return $this->getQuestions('linked',$ids,$requestParams);
	}

	/**
	 * Get the questions that are related to the questions identified by a set of $ids.
	 * @param $ids: Semicolon separated string with all question IDs you want to filter
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getRelatedQuestionsForQuestionsWithIDs($ids,$requestParams){
		return $this->getQuestions('related',$ids,$requestParams);
	}

	/**
	 * Get the timelines of the questions identified by a set of $ids.
	 * @param $ids: Semicolon separated string with all question IDs you want to filter
	 * @param $requestParams: The general request parameters
	 * @return Exception|string
	 */
	public function getTimelinesOfQuestionsWithIDs($ids,$requestParams){
		return $this->getQuestions('timeline',$ids,$requestParams);
	}

	/*
     * Global Object Methods
     */

	/**
	 * @param string $mode (default 'all'): Defines the mode under which the answers method should be used, see answers section under http://api.stackexchange.com/docs
	 *
	 * There are three modes
	 * mode 'all' corresponds to http://api.stackexchange.com/docs/answers
	 * mode 'selection' (*) corresponds to http://api.stackexchange.com/docs/answers-by-ids
	 * mode 'comments' (*) corresponds to http://api.stackexchange.com/docs/comments-on-answers
	 * @param string $idList: A list of answer ids to filter (mendatory for (*)-marked modes)
	 * @param array $requestParams: The general request parameters
	 * @return Exception|string
	 */
    protected function  getAnswers($mode='all', $idList='', $requestParams = array()) {
        $methodsArray = array();
        $mainMethodArray = array(
            'name' => 'answers'
        );

        switch ($mode){
            case 'selection':
	            $this->addMethodToMethodsArray($methodsArray,'answers',$idList);
                break;
            case 'comments':
	            $this->addMethodToMethodsArray($methodsArray,'answers',$idList);
	            $this->addMethodToMethodsArray($methodsArray,'comments');
                break;
            default:
	            $this->addMethodToMethodsArray($methodsArray,'answers');
                break;
        }

        $answers = $this->makeAPIRequest($methodsArray,$requestParams);
        return $answers;
    }

	/**
	 * @param string $mode (default 'all'): Defines the mode under which the questions method should be used, see questions section under http://api.stackexchange.com/docs
	 * There are ten modes
	 * mode 'all' corresponds to https://api.stackexchange.com/docs/questions
	 * mode 'selection' (*) corresponds to https://api.stackexchange.com/docs/questions-by-ids
	 * mode 'answers' (*) corresponds to https://api.stackexchange.com/docs/answers-on-questions
	 * mode 'comments' (*) corresponds to https://api.stackexchange.com/docs/comments-on-questions
	 * mode 'linked' (*) corresponds to https://api.stackexchange.com/docs/linked-questions
	 * mode 'related' (*) corresponds to https://api.stackexchange.com/docs/related-questions
	 * mode 'timeline' (*) corresponds to https://api.stackexchange.com/docs/questions-timeline
	 * mode 'featured' corresponds to https://api.stackexchange.com/docs/featured-questions
	 * mode 'unanswered' corresponds to https://api.stackexchange.com/docs/unanswered-questions
	 * mode 'no-answers' corresponds to https://api.stackexchange.com/docs/no-answer-questions
	 * @param string $idList (optional): A list of question ids to filter (mendatory for (*)-marked modes)
	 * @param array $requestParams: The general request parameters
	 * @return Exception|string
	 */
	protected function getQuestions($mode='all', $idList='', $requestParams = array()) {
		$methodsArray = array();

		switch ($mode){
			case 'selection':
				$this->addMethodToMethodsArray($methodsArray,'questions',$idList);
				break;

			case 'answers':
				$this->addMethodToMethodsArray($methodsArray,'questions',$idList);
				$this->addMethodToMethodsArray($methodsArray,'answers');
				break;

			case 'comments':
				$this->addMethodToMethodsArray($methodsArray,'questions',$idList);
				$this->addMethodToMethodsArray($methodsArray,'comments');
				break;

			case 'linked':
				$this->addMethodToMethodsArray($methodsArray,'questions',$idList);
				$this->addMethodToMethodsArray($methodsArray,'linked');
				break;

			case 'related':
				$this->addMethodToMethodsArray($methodsArray,'questions',$idList);
				$this->addMethodToMethodsArray($methodsArray,'related');
				break;

			case 'timeline':
				$this->addMethodToMethodsArray($methodsArray,'questions',$idList);
				$this->addMethodToMethodsArray($methodsArray,'timeline');
				break;

			case 'featured':
				$this->addMethodToMethodsArray($methodsArray,'questions');
				$this->addMethodToMethodsArray($methodsArray,'featured');

				break;

			case 'unanswered':
				$this->addMethodToMethodsArray($methodsArray,'questions');
				$this->addMethodToMethodsArray($methodsArray,'unanswered');
				break;

			case 'no-answers':
				$this->addMethodToMethodsArray($methodsArray,'questions');
				$this->addMethodToMethodsArray($methodsArray,'no-answers');
				break;

			default:
				$this->addMethodToMethodsArray($methodsArray,'questions');
				break;
		}

		$answers = $this->makeAPIRequest($methodsArray,$requestParams);
		return $answers;
	}

    /*
     * Fundamental Methods
     */

	/**
	 * addMethodToMethodsArray adds a new main or sub method to the methodsArray
	 * @param $methodsArray: The methodsArray where main & submethods are stored in. This method will
	 * update this array directly
	 * @param $methodName: The name of the method you want to add
	 * @param null $methodFilters (optional): An optional parameter used to submit an object ID filter for this method
	 */
	protected function addMethodToMethodsArray(&$methodsArray,$methodName,$methodFilters=null) {
		$methodArray = array(
			'name' => $methodName
		);

		if(isset($methodFilters)) {
			try {
				if($this->isValidFilterList($methodFilters)){
					$methodArray['filter'] = $methodFilters;
				}
			} catch (Exception $e) {
				//TODO: Think about an exception return strategy. At the moment simply output error message.
				echo 'Exception in '.$e->getFile().' at line '.$e->getLine().': '.$e->getMessage().$e->get;
			}
		}

		array_push($methodsArray,$methodArray);
	}

	/**
	 * isValidFilterList makes a raw validation check for filtering lists and throws
	 * corresponding exceptions if they are not valid
	 * @param $list: A list of object ids
	 * @return bool (is this a valid list)
	 * @throws Exception
	 */
	protected function isValidFilterList($list){
		if(!empty($list)) {
			if(is_string($list)){
				return true;
			} else {
				throw new Exception('The ID list should be a string!');
			}
		} else {
			throw new Exception('No answer IDs given to select!');
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
            for($i=0;$i<count($methodsArray);$i++){
                if($i > 0) {
                    $requestURL .= '/';
                }

                $requestURL .= $this->addMethodToURL($methodsArray[$i]);
            }
        }

        //If API Token is set, add it to the request parameters
        if(isset($this->token)) {
            $requestParams['key'] = $this->token;
        }

	    //If no filter is set, add withbody filter to provide support for more, than default filter, properties
	    if(!isset($requestParams['filter']) || empty($requestParams['filter'])){
		    $requestParams['filter'] = 'withbody';
	    }

        //Add params to $requestURL
        if(!empty($requestParams)){
           $requestURL .= $this->addParamsToURL($requestParams);
        }


        //Dev output $requestURL
        //var_dump($requestURL);

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
