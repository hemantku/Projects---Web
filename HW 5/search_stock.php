<!DOCTYPE html PUBLIC “@-//W3C//DTD XHTML 1.0 Strict//EN” “http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict-dtd”>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<title>CSCI 571 Homework #6</title>
	<script language="javascript">
	function validate()
	{
		if((document.forms["stock_searchForm"]["company"].value == "") || (document.forms["stock_searchForm"]["company"].value == NULL)){
			window.alert("Please enter a company symbol.");
			return false;

		}

	}
	</script>
	<style type="text/css">
            .stock_searchForm {
                height: 60px;
                width: 600px;
                border-style:solid;
		border-color:black;
                margin: 0 auto;
                padding: 20px;
            }

	 </style>
	
</head>
<body>
	<div style="text-align: center;"><h2>Market Stock Search</h2></div>
	<div class="stock_searchForm">
	<form name="stock_searchForm" onsubmit="return validate()" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                Company Symbol: <input type="text" name="company" value="<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {echo $_POST['company']; } ?>" style="width:350px;">
		<input class="input-rounded-button" style="width:80px;" type="submit" name="submit" value="Search"><br>
		<p style="padding-bottom: 50px;">Example: <i>GOOG, MSFT, YHOO, FB, AAPL, ...etc</i></p>

	</form>
	</div>
</body>
</html>
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		
		$URL = "http://query.yahooapis.com/v1/public/yql?";
$URL1 = "q=select%20Name%2C%20Symbol%2C%20LastTradePriceOnly%2C%20Change%2C%20ChangeinPercent%2C%20PreviousClose%2C%20DaysLow%2C%20DaysHigh%2C";
$URL2 = "%20OpenYearLow%2C%20YearHigh%2C%20Bid%2C%20Ask%2C%20AverageDailyVolume%2C%20OneyrTargetPrice%2C%20MarketCapitalization%20from";
		$URL3 = "%20yahoo.finance.quotes%20where%20symbol%3D%22";
		$URL4 = "%22&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
		$stockURL = $URL.$URL1.$URL2.$URL3.urlencode($_POST['company']).$URL4;
		//echo $stockURL;

		$headers = get_headers($stockURL);
		$errors = substr($headers[0], 9, 3);

		//echo "I am here";
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
					//echo $xmlData;
					
					//var_dump($xmlData);
					
					if($xmlData->results->quote->Change[0] == 0)
					{
						echo '<div style="text-align:center;"><h2>Stock Information Not Available.</h2></div>';

					}
					else
					{
						
						if($xmlData->results->quote->Change[0] >= 0)
						{
							$color = '#00FF00';
							$img = 'up_g.gif';
						}
						else
						{
							$color = '#FF0000';
							$img = 'down_r.gif';
						}
						$stockval=$xmlData->results->quote;
						//var_dump($stockval);
					$Name = $xmlData->results->quote->Name[0];
					$Symbol = $xmlData->results->quote->Symbol[0];
					$company_detail = array();
			$company_detail["PrevClose"] = number_format(doubleval($xmlData->results->quote->PreviousClose[0]),2);
			$company_detail["DaysLow"] = number_format(doubleval($xmlData->results->quote->DaysLow[0]),2);
			$company_detail["DaysHigh"] = number_format(doubleval($xmlData->results->quote->DaysHigh[0]),2);
			$company_detail["Bid"] = number_format(doubleval($xmlData->results->quote->Bid[0]),2);
			$company_detail["Change"] = preg_replace('/^[+]?||^[-]?/','',$xmlData->results->quote->Change[0]);
			$company_detail["YearLow"] = $xmlData->results->quote->YearLow[0];
		$b_or_m = substr($xmlData->results->quote->MarketCapitalization[0],-1);
	$company_detail["MarketCapitalization"] = number_format(doubleval($xmlData->results->quote->MarketCapitalization[0]),1).$b_or_m;
			
			$company_detail["LastTradePriceOnly"] = number_format(doubleval($xmlData->results->quote->LastTradePriceOnly[0]),2);
			$company_detail["PreviousClose"] = $xmlData->results->quote->PreviousClose[0];
			$company_detail["ChangeinPercent"] = preg_replace('/^[+]?||^[-]?/','',$xmlData->results->quote->ChangeinPercent[0]);
			$company_detail["OneyrTargetPrice"] = number_format(doubleval($xmlData->results->quote->OneyrTargetPrice[0]),2);
			$company_detail["YearHigh"] = number_format(doubleval($xmlData->results->quote->YearHigh[0]),2);
			
			$company_detail["Ask"] = number_format(doubleval($xmlData->results->quote->Ask[0]),2);
		        $company_detail["AverageDailyVolume"] = number_format(doubleval($xmlData->results->quote->AverageDailyVolume[0]),0);
			$company_detail["Open"] = number_format(doubleval($xmlData->results->quote->Open[0]),2);
			$company_detail["Volume"] = $xmlData->results->quote->Volume[0];
				
					
					echo '<div style="text-align:center;"><h2>Search Results</h2></div>';
echo '<p><font face="Times New Roman" size="+2"><strong>'.$Name.'</strong></font><font face="Verdana" size="+2"><strong>('.$Symbol.')</strong></font>'." ".$company_detail["LastTradePriceOnly"]." ".'<img src ='.$img.'>'." ".'<font color='.$color.'>'.$company_detail["Change"].'('.$company_detail["ChangeinPercent"].')</font>'.'</p>';
echo '<hr style="height:1px;border:2px solid;color:#333;background-color:#333;">';
echo '<table style="width:1270px; height=20px;">';
echo '<tr><td>Prev. Close:</td><td align="right">'.$company_detail["PrevClose"].'</td><td>Day&#39;s Range:</td><td align="right">'.$company_detail["DaysLow"].'-'.$company_detail["DaysHigh"].'</td></tr>';
echo '<tr><td>Open:</td><td align="right">'.$company_detail["Open"].'</td><td>52wk Range:</td><td align="right">'.$company_detail["YearLow"].'-'.$company_detail["YearHigh"].'</td></tr>';
echo '<tr><td>Bid:</td><td align="right">'.$company_detail["Bid"].'</td><td>Volume:</td><td align="right">'.$company_detail["Volume"].'</td></tr>';
echo '<tr><td>Ask:</td><td align="right">'.$company_detail["Ask"].'</td><td>Avg.Vol(3m):</td><td align="right">'.$company_detail["AverageDailyVolume"].'</td></tr>';
echo '<tr><td>1y Target Est.:</td><td align="right">'.$company_detail["OneyrTargetPrice"].'</td><td>Market Cap:</td><td align="right">'.$company_detail["MarketCapitalization"].'</td></tr></table>';	

			
					
		}
					
	}
		getlinks();
  }?>
<br><br><div style="text-align:center">
<?php
	function getlinks()
	{
	$URL5 = "http://news.yahoo.com/rss/2.0/headline?s=' '";
	$URL6="&region=LA&lang=en-US";
	$RSSURL = $URL5.$URL6;
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
					 //echo $xmlData1->channel->item->link[0];
					//var_dump($xmlData1);
					//echo $xmlData1->channel->title;
					if($xmlData1->channel->title == "Yahoo! Finance: RSS feed not found")
					{
					
						echo '<div style="text-align:center;"><h2>Financial Company News is not available.</h2></div>';
					}
					else
					{
						$i=0;
						$linksarray = array();
						foreach($xmlData1->channel->item as $child)
            					{
						  //echo $child->link."<br/>;";
						  //echo $child->title."<br/>;";
					          $linksarray[$i][0]= $child->title;
                				  $linksarray[$i][1]= $child->link;
						  //echo "Here is the stored".$linksarray[$i][0]."<br/>;";
						  //echo "Here is the stored".$linksarray[$i][1]."<br/>;";
                				  $i++;
            					}
						//echo $i;
						$count = $i;
      echo '<div style="text-align:left;"><font face="Times New Roman" size="+2"><strong>News Headlines</strong></font></div>';		
	echo '<hr style="height:1px;border:2px solid;color:#333;background-color:#333;">';
	echo '<ul>';
	for($i = 0; $i < $count-1; $i++)
	{
		echo '<li><a href="'.$linksarray[$i][1].'"</a>'.$linksarray[$i][0].'</li>';				
	}
	echo '</ul>';
	}		  
	}

	}?></div><br>
