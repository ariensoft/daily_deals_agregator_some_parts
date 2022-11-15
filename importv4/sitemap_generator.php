<?php

$ch = curl_init("http://meslevy.cz/site/sitemap");
$fp = fopen("/var/www/msv4/web/sitemap.xml", "w"); // soubor na webu.
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
}

$ch = curl_init("http://setrilci.cz/site/sitemap");
$fp = fopen("/var/www/v2_setrilci/web/sitemap.xml", "w"); // soubor na webu.
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
}