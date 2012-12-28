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
			$outputKeys = $methodOutputKeyListArray[$index];

			//Save them in an array
			$outputKeyArray = explode(',',(string)$outputKeys);

			//Foreach result object..
			foreach($requestData['items'] as $item){
				$itemID = (isset($item[$requestDataType.'_id'])?$item[$requestDataType.'_id']:null);
				$stackAPIOutput .= '<div class="stackexchange-item"';
					if(isset($itemID)){
						$stackAPIOutput .= 'id="stackexchange-'.$requestDataType.'-'.$itemID.'"';
					}
				$stackAPIOutput .= '>';

				//Set link to the requested object url
				if(isset($item['link']) && isset($item['title']) && isset($item['creation_date'])) {
					$stackAPIOutput .= '<div class="stackexchange-object-link-wrapper">';
						$stackAPIOutput .= '<a href="'.$item['link'].'" title="'.$item['title'].'" class="stackexchange-object-link" target="_blank"';
							if(isset($itemID)){
								$stackAPIOutput .= 'id="'.$requestDataType.'-'.$itemID.'-link"';
							}
						$stackAPIOutput .= '>';
						$stackAPIOutput .= date('d.m.Y',$item['creation_date']);
						$stackAPIOutput .= '</a>';
					$stackAPIOutput .= '</div>';
				}

					//... output the requested property keys
					foreach($outputKeyArray as $key){
						$valueForKey = $item[$key];
						if(isset($valueForKey)){
							$stackAPIOutput .= '<div class="'.$key.'"';
								if(isset($itemID)){
									$stackAPIOutput .= 'id="'.$requestDataType.'-'.$itemID.'-'.$key.'"';
								}
							$stackAPIOutput .= '>';
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
										$stackAPIOutput .= '<div class="'.$arrayKey.'"';
											if(isset($itemID)) {
												$stackAPIOutput .= 'id="'.$key.'-'.$itemID.'-'.$arrayKey.'"';
											}
										$stackAPIOutput .= '>';
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
