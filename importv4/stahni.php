<?php 
include_once 'connect.php';

 $ziskejadresu = mysql_query("select ServerId, FeedUrl, Name, FeedType from Servers WHERE FeedType LIKE 'skrz'");
 
 while ($zaznam=mysql_fetch_array($ziskejadresu)): 

$adresaxml = $zaznam["FeedUrl"];
$jmenoserveru = $zaznam["ServerId"]; 
$jmenosouboru = $jmenoserveru.".xml";   
 //echo $adresaxml." opening.. \n";
$ch = curl_init("$adresaxml");
$fp = fopen("/var/webdata/feeds/$jmenosouboru", "w"); // soubor na webu.
if(!$fp) echo "Nepodarilo se vytvorit soubor. Jsou nastavena pristupova prava?";
if(!$ch) echo "Nepodarilo se spojit se serverem.";
if($fp&&$ch){
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
//echo $adresaxml.": OK! \n";
    
}

 endwhile; 




















?>
