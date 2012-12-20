<?
	//Create new stackexchangeAPI Object (insert your API Key here as parameter, e.g.new stackexchange('XXXXXXX'))
	$stackExchangeAPI = new stackexchange();

	//Fetch values from current page object
	$siteList = (string)(str_replace(' ','',$page->stackexchangesite()));
	$siteArray = explode(',',$siteList);

	$methodList = (string)(str_replace(' ','',$page->stackexchangemethod()));
	$methodArray = explode(',',$methodList);

	$methodFilterList = (string)(str_replace(' ','',$page->stackexchangefilter()));
	$methodFilterArray = explode(',',$methodFilterList);

	$methodFilteredIDList = (string)(str_replace(' ','',$page->stackexchangemethodids()));
	$methodFilteredIDArray = explode(',',$methodFilteredIDList);

	$methodOutputKeyList = (string)(str_replace(' ','',$page->stackexchangeoutput()));
	$methodOutputKeyListArray = explode(';',$methodOutputKeyList);

	//Start output
	$stackAPIOutput = '';
	$stackAPIOutput .= '<div class="stackexchange-wrapper">';

	foreach($methodArray as $index => $method){

		//For each method...

		//Init parameters
		$params = array();

		if(isset($siteArray[$index])){
			$site = $siteArray[$index];
			$params['site'] = $site;
		}

		//Initiate requestData & requestDataType variable
		$requestData = null;
		$requestDataType = null;

		//Add method specific filters if given by the user
		if(isset($methodFilterArray[$index])){
			$requestFilter = $methodFilterArray[$index];
			$params['filter'] = $requestFilter;
		}

		//Add request logic
		switch($method){
			case 'answers':
				$requestData = $stackExchangeAPI->getAllAnswers($params);
				$requestDataType = 'answer';
				break;
			case 'answers-by-ids':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getAnswersByIDs($ids,$params);
					$requestDataType = 'answer';
				}
				break;
			case 'comments-on-answers':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getCommentsForAnswersWithIDs($ids,$params);
					$requestDataType = 'comment';
				}
				break;
			case 'questions':
				$requestData = $stackExchangeAPI->getAllQuestions($params);
				$requestDataType = 'question';
				break;
			case 'questions-by-ids':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getQuestionsByIDs($ids,$params);
					$requestDataType = 'question';
				}
				break;
			case 'answers-on-questions':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getAnswersForQuestionsWithIDs($ids,$params);
					$requestDataType = 'answer';
				}
				break;
			case 'comments-on-questions':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getCommentsForQuestionsWithIDs($ids,$params);
					$requestDataType = 'comment';
				}
				break;
			case 'linked-questions':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getLinkedQuestionsForQuestionsWithIDs($ids,$params);
					$requestDataType = 'question';
				}
				break;
			case 'related-questions':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getRelatedQuestionsForQuestionsWithIDs($ids,$params);
					$requestDataType = 'question';
				}
				break;
			case 'questions-timeline':
				if(isset($methodFilteredIDArray[$index])){
					$ids = $methodFilteredIDArray[$index];
					$requestData = $stackExchangeAPI->getTimelinesOfQuestionsWithIDs($ids,$params);
					$requestDataType = 'question_timeline';
				}
				break;
			case 'featured-questions':
				$requestData = $stackExchangeAPI->getFeaturedQuestions($params);
				$requestDataType = 'question';
				break;
			case 'unanswered-questions':
				$requestData = $stackExchangeAPI->getUnansweredQuestions($params);
				$requestDataType = 'question';
				break;
			case 'no-answer-questions':
				$requestData = $stackExchangeAPI->getQuestionsWithNoAnswers($params);
				$requestDataType = 'question';
				break;
			default:
				echo 'default';
				break;
		}

		//If the request fetched some data
		if(isset($requestData)){
			//Get all requested object keys
			$outputKeys = $methodOutputKeyListArray[$index];

			//Save them in an array
			$outputKeyArray = explode(',',(string)$outputKeys);

			//Foreach result object..
			foreach($requestData['items'] as $item){
				$itemID = $item[$requestDataType.'_id'];
				$stackAPIOutput .= '<div id="stackexchange-'.$requestDataType.'-'.$itemID.'" class="stackexchange-item">';

				//Set link to the requested object url
				if(isset($item['link']) && isset($item['title']) && isset($item['creation_date'])) {
					$stackAPIOutput .= '<div class="stackexchange-object-link-wrapper">';
						$stackAPIOutput .= '<a href="'.$item['link'].'" title="'.$item['title'].'" id="'.$requestDataType.'-'.$itemID.'-link" class="stackexchange-object-link" target="_blank">';
						$stackAPIOutput .= date('d.m.Y',$item['creation_date']);
						$stackAPIOutput .= '</a>';
					$stackAPIOutput .= '</div>';
				}

					//... output the requested property keys
					foreach($outputKeyArray as $key){
						$valueForKey = $item[$key];
						if(isset($valueForKey)){
							$stackAPIOutput .= '<div id="'.$requestDataType.'-'.$itemID.'-'.$key.'" class="'.$key.'">';
							if(!is_array($valueForKey)){
								$stackAPIOutput .= $valueForKey;

							} else {
								if($key == 'owner'){
									$stackAPIOutput .= '<div class="stackexchange-user-banner-wrapper">';
										$stackAPIOutput .= '<a href="'.$valueForKey['link'].'" class="stackexchange-user-banner">';
											$stackAPIOutput .= '<img class="user-pic" src="'.$valueForKey['profile_image'].'"/>';
											$stackAPIOutput .= '<span class="user-name">'.$valueForKey['display_name'].'</span>';
										$stackAPIOutput .= '</a>';
									$stackAPIOutput .= '</div>';
								} else {
									foreach($valueForKey as $arrayKey => $arrayKeyValue){
										$stackAPIOutput .= '<div id="'.$key.'-'.$itemID.'-'.$arrayKey.'" class="'.$arrayKey.'">';
											$stackAPIOutput .= $arrayKeyValue;
										$stackAPIOutput .= '</div>';
									}
								}
							}
							$stackAPIOutput .= '</div>';
						}
					}

				$stackAPIOutput .= '</div>';
			}
		}
	}

	$stackAPIOutput .= '</div>';
	echo $stackAPIOutput;
?>
