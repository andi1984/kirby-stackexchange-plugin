<?

	if($page->stackexchangesite() != null && $page->stackexchangemethod() != null){
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

			//Check whether a site ID is specified to search at --> add it as 'site' URL-parameter
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

			//Execute API request and save result
			$result = $stackExchangeAPI->executeMethodWithURLPathName($method, $params, $methodFilteredIDArray[$index]);

			//Save data and data-type inside corresponding variables
			$requestData = $result['data'];
			$requestDataType = $result['data-type'];

			//If the request fetched some data --> output data
			if(isset($requestData)){
				//Get all requested object keys
				$outputKeyList = $methodOutputKeyListArray[$index];
				if(!empty($methodOutputKeyList)){
					//Save them in an array
					$outputKeys = explode(',',(string)$outputKeyList);

					//Foreach result object..
					foreach($requestData['items'] as $item){
						$stackAPIOutput .= $stackExchangeAPI->defaultLayout($item, $requestDataType, $outputKeys);
					}
				}
			}
		}

		$stackAPIOutput .= '</div>';
		echo $stackAPIOutput;
	}

?>
