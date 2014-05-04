<?php

function main()
{
   $value = trim($_POST["mySelect"]);
  // echo "value ..".$value;
   
     if(strlen($_POST['code'])===0)
      {
        //alert box
        echo "<alert('Please enter a valid zip code')>";
      }

   if($value==='city')
   {
     $city = $_POST['code']; 
     $deg= $_POST['degree'];
     $city=urlencode($city);
     $d= "http://where.yahooapis.com/v1/places\$and(.q('".$city."'),.type(7));start=0;count=5?appid=1.o7nELV34GbDDpfA4AuP6mkJeFZJz0S43rXhMo59oPLZTbQqud9uXGKyR6J8Rqd"; 
     
     //$a="http://where.yahooapis.com/v1/places\$and(.q('" ;
    // $b="'),.type(7));start=0;count=5?appid=1.o7nELV34GbDDpfA4AuP6mkJeFZJz0S43rXhMo59oPLZTbQqud9uXGKyR6J8Rqd";
    // $d=$a.$city.$b;  
     //echo "query sent for city -> ".$d." <br/>";

    // $content= file_get_contents($d);
    $content= @simplexml_load_file($d);
    //print_r($content);
   // echo "*******  ".$content."  ***********";
    
        	if($content === false)
            {
				echo "<p>Zero Result Found.</p>";
			}
             else
             {
             $i=0;
             $arraywoeid= array();
             foreach($content->place as $child)
             {
                  $arraywoeid[$i]= $child->woeid;
                  $i++;
             }
                   
                  $count=count($content);
                if($count===0)
                {
                    
                    echo "<p>Zero Result Found.</p>";
                }   
                else
                {         
                    $city=$_POST['code'];
                    $print= "<br/>$count result(s) for $city ";
                    $print.='<div><table border="1"  width="100%"><th>Weather</th><th> Temperature</th><th> City</th><th> Region</th><th> Country</th><th> Latitude</th><th> Longitude</th><th> Links to Details</th>';
                    foreach($arraywoeid as $x=>$x_woeid)
                    {
                   //  echo "Key=" . $x . ", Value=" . $x_woeid;
                    $print.=  @getWeather($x_woeid,$deg);
                                           
                    }
           // <count> result(s) <City|Zip Code> <value of location text field>].
                  
                    $print.='</table></div>'; 
                    echo $print;
                // echo "</table>";
                }
        }
    }
else 
    {                                              
        //for ZIP code
        
       if(is_numeric($_POST['code']) )
      {
        if(strlen($_POST['code'])===5)
        {
           echo "<alert('Please enter a valid zip code')>"; 
        }
      }
        
        $zip = $_POST['code'];     
        $deg= $_POST['degree'];
        $a="http://where.yahooapis.com/v1/concordance/usps/" ;
        $b= "?appid=1.o7nELV34GbDDpfA4AuP6mkJeFZJz0S43rXhMo59oPLZTbQqud9uXGKyR6J8Rqd";
        $c=$a.$zip.$b;       
        //echo "test----input ".$c;
        $content= @file_get_contents($c);
        	if($content === false)
            {
				echo "<p>Zero Result Found.</p>";
			}
        else
        {
         $xml =new SimpleXMLElement($content); 
         $woeid1= ($xml->woeid);
        // echo "value of woeid".$woeid1;
                    $print=' ';
                    $count="1";
                    echo "<br/>$count result(s) for zip $zip ";
                    $woeid= ($xml->woeid);
                    $print.='<div><table border="1"  width="100%"><th>Weather</th><th> Temperature</th><th> City</th><th> Region</th><th> Country</th><th> Latitude</th><th> Longitude</th><th> Links to Details</th>';
                    $print.=@getWeather($woeid,$deg);
                    $print.='</table></div>'; 
                    echo $print;
                     
        }
   }
  

}



function getWeather($woeid, $tempunit)
{     
    
     $url= "http://weather.yahooapis.com/forecastrss?w=";
     $url1="&u=";
     $newoeid=trim($woeid);
     //echo " ".$newoeid." ";
     $url2=trim($url.$newoeid.$url1.$tempunit);
     //echo "url 2nd time send--->".$url2;
     $doc = @file_get_contents($url2);  
     if($doc === false)
     {
			echo "<p>Zero Result found</p>";
	 }
     else
     {
         $sxml = new SimpleXMLElement($doc);
        $print='';
      $print.= @output($sxml);
    
      return $print;
     }
 }
 
function output($sxml)
{
        $weather_panel="NA";
      
        $temperature="NA";
       
        $city="NA";
        
        $region="NA";
        
        $country="NA";
       
        $geolat="NA";
      
        $link="NA";
     
    
        $namespaces = $sxml->getNameSpaces(true);
		$yweather = $sxml->channel->item->children($namespaces['yweather']);
        $location = $sxml->channel->children($namespaces['yweather'])->location;
        
        $text=$sxml->channel->children($namespaces['yweather'])->text;
        $temperature = $yweather->condition->attributes()->text." ".$yweather->condition->attributes()->temp."<sup>o</sup> ".$sxml->channel->children($namespaces['yweather'])->units->attributes()->temperature;
        if(strlen($yweather->condition->attributes()->temp)==="o")
        $temperature="NA";     
        $city=$location->attributes()->city;
        $region= $location->attributes()->region;
        $country=$location->attributes()->country;
        
       
       $desc = $sxml->channel->item->description;  
        
        $pattern = '/src="(.*?)"/i';  
        @preg_match($pattern, $desc, $m);  
        $weather['url']= $m[1];  
       
        
       	$geo = $sxml->channel->item->children($namespaces['geo']);
        	$geolat =$geo->lat;
        $geolong =$geo->long;
        
        
        $geopattern='http://us.rd.yahoo.com/dailynews/rss/weather/i';
        $link1= $sxml->channel->link;
        @preg_match($geopattern, $link1, $p);
        $det['link'] = $p[1];
        $link= '<a href="'.$link1.'"> Details</a>'; 
	
        
      
      
       $date = $yweather->condition->attributes()->date;       
       if(strlen($weather['url']) === 0)
       {
            $weather_panel= '<a href="'.$link1.'" ><img  src = "'.$text.'" alt="?"  height="42" width="42"/></a>';
       }
       else
       {
            $weather_panel= '<a href="'.$link1.'" ><img  src = "' . $weather['url'] . '" alt="'.$text.'" /></a>';
       }
        if($weather_panel==="")
        $weather_panel.="NA";
        if(strlen($temperature)===0)
        $temperature.="NA";
        if(strlen($city)===0)
        $city.="NA";
        if(strlen($region)===0)
        $region.="NA";
        if(strlen($country)===0)
        $country.="NA";
        if (strlen($geolat)===0)
        $geolat.="NA";
        if (strlen($geolong)===0)
        $geolong.="NA";
        if(strlen($link)===0)
        $link.="NA";
   
       $tab='';
        $tab .='<div>';
        $tab .= '<tr ><td>';
        $tab .= $weather_panel;
        $tab .= '</td><td>';
        $tab .= $temperature;
        $tab .= '</td><td>';
        $tab .=$city;
        $tab .= '</td><td>';
        $tab .= $region;
        $tab .= '</td><td>';
        $tab .= $country;
        $tab .= '</td><td>';
        $tab .= $geolat;
        $tab .= '</td><td>';
        $tab .= $geolong;
        $tab .= '</td><td>';
        $tab .= $link;
        $tab .= '</td></tr>';
        $tab.='</div>';
       
return $tab;
}
?>


<html>
<head>
<!--Created by Akshay Anand Fall 2013 MS CSCI 571/ email: akshayan@usc.edu-->
    <title> Homework #6</title>
<style>
    
    
.tr_Row
{
 
text-align :center;
margin-left:auto;
margin-right:auto;
width:330px;
}

.table_Style
{
font-style:normal;
font-size:14px;
font-weight:bold;
border:4px;
margin-left:auto;
margin-right:auto;
text-align :center;
width:300px;   
border-collapse:collapse; 
border-bottom-style:ridge;
border-right-style:ridge;
}

.td_Column
{
font-style:normal;
font-size:14px;
font-weight:bold;
border-width:3px;

border-style: ridge;
border-color: #B4B4B4;
margin-left:auto;
margin-right:auto;
vertical-align:top;
}


.td_Column1
{
font-style:normal;
font-size:14px;
font-weight:bold;
border-width:3px;
border-left:3px ;
border-bottom:3px ;
border-top:3px ;
border-right-width:0px ;
border-style: ridge;
border-color: #B4B4B4;
margin-left:auto;
margin-right:auto;
vertical-align:top;
}

.bg_Color
{
    color:Black;
text-decoration: none;
}
    
    .centerAlign
    {
        width:80%;
        text-align:left;
    }
    
    .centerAlign
    {
        margin-left:30%;    
    }
     .centerAlign45
    {
        margin-left:45%;    
    }
     .centerAlign40
    {
        margin-left:40%;    
    }
    .heightLine
    {
          height:20px;
    }
    .widthMid
    {
        width:450px;
            margin-bottom: 0px;
        }
   
    .widthButton
    {
        width:70px;
    }
    </style>

<script type="text/javascript">
var flag = false;

function getFlag()
{
    return flag;
}

function verify()
    {
       //alert("IN");
       var textbox = document.getElementById("location").value;
       //alert(textbox.length); 
        if (textbox=="")
           {
                   alert('Please specify a city name/ zipcode!');
                   return false;
           }
//
           var mySelect = document.getElementById('mySelect').value;
            if( mySelect=="zip")
            {   
                //alert('hello');
                if(textbox.length!=5)
                {
                    flag= false;
                }
                
                var regex_zip="!/^\d{5}$/";
                if(textbox.match(/^[0-9]{5}$/g))
                {
                    //alert('zip CORRECT');
                    flag= true;
                }
                else 
                {
                    alert('Zipcode incorrect!');
                    flag= false;
                }
             }
             
            else if( mySelect=="city")
            {   
                //alert('city');
                var regex_city="[\w ]+";
              
            
                if(!textbox.match(/[0-9]+/g))
                {
                      //  alert('CITY CORRECT');
                        flag= true;
                }
                else
                {
                    alert('CITY INCORRECT');
                    flag= false;
                }                
             }
             
    }
  </script>
  
</head>

<body>

  <?php if(count($_POST)!==0): 
 
  main();
  
  else: ?>
  <div class="centerAlign40"> Weather Search</div><br/>
     <form  name="searchform" method="post" id="form"  onsubmit="return getFlag();" action="get_weather.php">
     
                  <table class="tr_Row">

                    <tr class="tr_Row">
                     
                    </tr>

                    <tr>
                      <td>
                        <table class="table_Style">
                          <tr>
                            <td class="td_Column">
                              Location : 
                            </td>
                            <td class="td_Column">
                              <input id="location" type="text"   name="code" runat="server"/>
                            </td>
                          </tr>

                          <tr>
                            <td class="td_Column">
                              Location Type : 
                            </td>

                            <td class="td_Column">
                              <select id="mySelect" name="mySelect">
                                <option value="city"  selected="true">City</option>
                                <option value="zip" >Zip</option>
                              </select>
                            </td>
                          </tr>

                          <tr>
                            <td class="td_Column">
                              Temperature Unit :
                             
                            </td>
                            <td class="td_Column">
                              <input type="radio" name="degree" checked="true" value="f"/>Fahrenhite
                              <input type="radio" name="degree" value="c"/>Celsius
                            </td>
                          </tr>

                          <tr >
                            <td class="td_Column1" >
                              <input class="centerAlign" name="submit" type="submit" value="submit" onclick="verify()"/>
                            </td>
                          </tr>

                        </table>
                      </td>
                    </tr>
                  </table>

                </form>


  <?php endif; ?>

</body>

</html>
