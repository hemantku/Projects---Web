<?php
	$URL = "http://query.yahooapis.com/v1/public/yql?";
	$URL1 = "q=select%20Name%2C%20Symbol%2C%20LastTradePriceOnly%2C%20Change%2C%20ChangeinPercent%2C%20PreviousClose%2C%20DaysLow%2C%20DaysHigh%2C";
	$URL2 = "%20OpenYearLow%2C%20YearHigh%2C%20Bid%2C%20Ask%2C%20AverageDailyVolume%2C%20OneyrTargetPrice%2C%20MarketCapitalization%20from";
		$URL3 = "%20yahoo.finance.quotes%20where%20symbol%3D%22";
		$URL4 = "%22&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
		$stockURL = $URL.$URL1.$URL2.$URL3.urlencode($_GET['symbol']).$URL4;

		//echo $stockURL;

		$headers = get_headers($stockURL);
		$errors = substr($headers[0], 9, 3);

		if($errors != "404"){
					//echo "I am here-2";
					$xmlStr = file_get_contents($stockURL);
					if ($xmlStr === false) {
						die('Request failed');
					}
					//$xmlStr = urlencode($stockURL);
					$xmlData = simplexml_load_string($xmlStr);
					if ($xmlData === false) {
							die('Parsing failed');
					}
					$response = new SimpleXMLElement('<result></result>');
					$response->addChild('Name', $xmlData->results->quote->Name[0]);
					$response->addChild('Symbol', $xmlData->results->quote->Symbol[0]);
					$responsenew = $response->addchild('Quote');
					if($xmlData->results->quote->Change[0] >= 0)
						{
							$responsenew->addChild('ChangeType', '+');
						}
						else
						{
							$responsenew->addChild('ChangeType', '-');

						}
					$responsenew->addChild('Change', $xmlData->results->quote->Change[0]);
					$responsenew->addChild('ChangeinPercent', preg_replace('/^[+]?||^[-]?/','',$xmlData->results->quote->ChangeinPercent[0]));
					$responsenew->addChild('LastTradePriceOnly', number_format(doubleval($xmlData->results->quote->LastTradePriceOnly[0]),2));
					$responsenew->addChild('PreviousClose',$xmlData->results->quote->PreviousClose[0]);
					$responsenew->addChild('DaysLow', number_format(doubleval($xmlData->results->quote->DaysLow[0]),2));
					$responsenew->addChild('DaysHighs', number_format(doubleval($xmlData->results->quote->DaysHigh[0]),2));
					$responsenew->addChild('Open', number_format(doubleval($xmlData->results->quote->Open[0]),2));
					$responsenew->addChild('YearLow', $xmlData->results->quote->YearLow[0]);
					$responsenew->addChild('YearHigh', number_format(doubleval($xmlData->results->quote->YearHigh[0]),2));
					$responsenew->addChild('Bid', number_format(doubleval($xmlData->results->quote->Bid[0]),2));
					$responsenew->addChild('Volume', $xmlData->results->quote->Volume[0]);
					$responsenew->addChild('Ask', number_format(doubleval($xmlData->results->quote->Ask[0]),2));
					$responsenew->addChild('AverageDailyVolume', number_format(doubleval($xmlData->results->quote->AverageDailyVolume[0]),0));
					$responsenew->addChild('OneyrTargetPrice', number_format(doubleval($xmlData->results->quote->OneyrTargetPrice[0]),2));
					$b_or_m = substr($xmlData->results->quote->MarketCapitalization[0],-1);
					$responsenew->addChild('MarketCapitalization',number_format(doubleval($xmlData->results->quote->MarketCapitalization[0]),1).$b_or_m);
					$responsenew1 = $response->addChild('News');

				}
				$URL5 = "http://feeds.finance.yahoo.com/rss/2.0/headline?s=";
				$URL6="&region=US&lang=en-US";
				$RSSURL = $URL5.urlencode($_GET['symbol']).$URL6;
				$headers1 = get_headers($RSSURL);
				$errors1 = substr($headers1[0], 9, 3);

					if($errors1 != "404"){
					$xmlStr1 = file_get_contents($RSSURL);
					if ($xmlStr1 === false) 
				        {
					die('Request failed');
					}
					
					$xmlData1 = simplexml_load_string($xmlStr1);
					if ($xmlData1 === false) 
					{
							die('Parsing failed');
					}
						
						//foreach($xmlData1->channel->item as $child)
            			//{
            					//$responsenew2 = $responsenew1->addChild('Item');
            					//$responsenew2->addChild('Title',$child->title);
            					//$responsenew2->Link = htmlspecialchars($child->link,ENT_QUOTES);
            			//}
            	 
            	 $URL7 = "http://chart.finance.yahoo.com/t?s=";
            	 $URL8 = "&amp;lang=en-US&amp;amp;width=300&amp;height=180";

            	 $ImageURL = $URL7.urlencode($_GET['symbol']).$URL8;
            	 //echo $ImageURL;

            	 $response->addChild('StockChartImageURL',htmlspecialchars($ImageURL,ENT_QUOTES));
            	 echo $response->asXML();

            	 }	
					            		

?>
