<?php

mb_internal_encoding('utf-8');

include_once 'simple_html_dom_15.php';
include_once 'GetImage.php';
include_once 'connect.php';

$servers_data = array();

$serversSQL = mysql_query("SELECT Struktury.*, Servers.Name as Name, Servers.FeedUrl as FeedUrl FROM Struktury LEFT JOIN Servers on Servers.ServerId = Struktury.ServerId");
$pocet = mysql_num_rows($serversSQL);
if ($pocet > 0) {
    while ($server = mysql_fetch_array($serversSQL)) :
        $servers_data[$server['ServerId']] = $server;
    endwhile;
}

$dnesni_datum = Date("Y-m-d", Time());

$MainDealsSQL = mysql_query("SELECT * FROM Slevy WHERE `DStart` = '$dnesni_datum' AND Status IN (1,2)");
//$MainDealsSQL = mysql_query("SELECT * FROM Slevy");
//$MainDealsSQL = mysql_query("SELECT * FROM Slevy WHERE `ServerId` = 65");
//$MainDealsSQL = mysql_query("SELECT * FROM Slevy WHERE Status = 2 AND Rating > 0 ORDER BY `Slevy`.`DStart` DESC");
$pocetMainDeals = mysql_num_rows($MainDealsSQL);
if ($pocetMainDeals > 0) {

    while ($md = mysql_fetch_array($MainDealsSQL)) :

        if (isset($servers_data[$md['ServerId']])) {
            $new_file = stahni_url($md['Url'], $md['DealId']);
            $description = $servers_data[$md['ServerId']]['Description'];
            $perex = $servers_data[$md['ServerId']]['Perex'];
            $images = $servers_data[$md['ServerId']]['Images'];
            $address_source = $servers_data[$md['ServerId']]['AddressSource'];
            $address = $servers_data[$md['ServerId']]['AddressTag'];
            $description_source = $servers_data[$md['ServerId']]['DescriptionSource'];
            $description_tag = $servers_data[$md['ServerId']]['DescriptionTag'];
            $dId = $md['DealId'];
            $deal_hash = $md['Hash'];
            $hash_parts = [];
            $hash_parts = explode('][', $deal_hash);
            $owner_dId = str_replace(']', '', $hash_parts[1]);
            $xml_path = $md['ServerId'] . '.xml';

            if ($new_file !== FALSE) {

                $html = file_get_html("/var/webdata/pages/" . $new_file);

                if ($html) {
                    if (!empty($perex)) {
                        if ($html->find($perex, 0)) {
                            $perex_db = $html->find($perex, 0)->plaintext;
                            $perex_db = html_entity_decode($perex_db, ENT_NOQUOTES, 'UTF-8');
                            mysql_query("UPDATE SlevyMeta SET Perex = '" . mysql_real_escape_string(trim($perex_db)) . "' WHERE DealId = $dId") or die("Chyba updatu $dId: " . mysql_error());
                        }
                    } else {
                        if ($html->find($description, 0)) {
                            $plain_text = $html->find($description, 0)->plaintext;
                            $plain_text_ed = html_entity_decode($plain_text, ENT_NOQUOTES, 'UTF-8');
                            $endings = array(".", "!", "?");
                            $plain_text_replaced = str_replace($endings, "[explode]", $plain_text_ed);
                            $plain_text_exp = explode("[explode]", $plain_text_replaced);
                            $perex_db = $plain_text_exp[0];
                             mysql_query("UPDATE SlevyMeta SET Perex = '" . mysql_real_escape_string(trim($perex_db)) . "' WHERE DealId = $dId") or die("Chyba updatu $dId: " . mysql_error());
                        }
                    }

                    $address_db = '';
                    if ($address_source === 'xml') {
                        $address_db = fetch_xml($xml_path, $address, $owner_dId);
                        if ($address_db !== FALSE) {
                            mysql_query("UPDATE SlevyMeta SET Address = '" . mysql_real_escape_string(trim($address_db)) . "' WHERE DealId = $dId") or die("Chyba updatu $dId: " . mysql_error());
                        }
                    } else {
                        if ($address_source === 'web') {
                            if ($html->find($address, 0)) {
                                $address_db = $html->find($address, 0)->plaintext;
                                mysql_query("UPDATE SlevyMeta SET Address = '" . mysql_real_escape_string(trim($address_db)) . "' WHERE DealId = $dId") or die("Chyba updatu $dId: " . mysql_error());
                            }
                        }
                    }

                    $desc_full_db = '';
                    if ($description_source === 'xml') {
                        $desc_full_db_pure = fetch_xml($xml_path, $description_tag, $owner_dId);
                        if ($desc_full_db_pure !== FALSE) {
                            $desc_full_db = html_entity_decode($desc_full_db_pure, ENT_NOQUOTES, 'UTF-8');
                            mysql_query("UPDATE SlevyMeta SET DescriptionFull = '" . mysql_real_escape_string(trim($desc_full_db)) . "' WHERE DealId = $dId") or die("Chyba updatu $dId: " . mysql_error());
                        }
                    } else {
                        if ($html->find($description, 0)) {
                            $testa = $html->find($description, 0)->innertext;
                            $testa = html_entity_decode($testa, ENT_NOQUOTES, 'UTF-8');
                            mysql_query("UPDATE SlevyMeta SET DescriptionFull = '" . mysql_real_escape_string(trim($testa)) . "' WHERE DealId = $dId") or die("Chyba updatu $dId: " . mysql_error());
                        }
                    }
                    /* $testb = htmlentities($testa, ENT_NOQUOTES, 'UTF-8');
                      $testc = str_replace("&nbsp;", " ", $testb);
                      $testd = str_replace("&Acirc;", " ", $testc);
                      $testr = html_entity_decode($testd, ENT_NOQUOTES, 'UTF-8');
                      $test = preg_replace('!\s+!', ' ', $testr);
                      $longText = mysql_real_escape_string($test); */
                    if ($html->find($images, 0)) {
                        $existing_images = [];

                        $imagesSQL = mysql_query("SELECT * FROM SlevyObrazky WHERE DealId = $dId");
                        $pocet_images = mysql_num_rows($imagesSQL);
                        if ($pocet_images > 0) {
                            while ($eimages = mysql_fetch_array($imagesSQL)) :
                                $existing_images[] = $eimages['Url'];
                            endwhile;
                        }

                        $ic = 0;
                        foreach ($html->find("$images img") as $img) {
                            $server_name = mb_strtolower($servers_data[$md['ServerId']]['Name']);
                            $image_path = (string) $img->src;
                            $image_address = "http://images.meslevy.cz/none.jpg";
                            if (!preg_match("/http/iu", $image_path)) {
                                $image_path = 'http://' . $server_name . (string) $img->src;
                            }

                            if (!in_array($image_path, $existing_images) && !preg_match("/fikyfik/iu", $image_path)) {
                                $image = new GetImage;
                                $image->source = trim($image_path);
                                $image->save_to = '/var/webdata/images/deals/';  // with trailing slash at the end
                                $image->custom_name = $dId . "_" . $ic;

                                $get = $image->download('gd'); // using GD

                                if ($get != false) {
                                    //echo $image_path . "\n";
                                    $image_address = "http://images.meslevy.cz/deals/" . $get;
                                    mysql_query("INSERT INTO `ms_import`.`SlevyObrazky` (`id`, `DealId`, `Url`, `UrlLocal`) VALUES (NULL, '$dId', '" . $image_path . "', '" . $image_address . "')") or die("chyba insertu obrazku: " . mysql_error());
                                }
                            }
                            $ic++;
                        }

                        unset($existing_images);
                    }
                } else {
                    echo "FAILED TO GET ELEMENT: " . $md['Url'] . " \n";
                }
            } else {
                echo "PAGE DOWNLOAD FAILED: " . $md['DealId'] . " \n";
            }
        }
    endwhile;
}

function stahni_url($url, $deal_id, $fail = FALSE) {

    $jmenosouboru = $deal_id . '.htm';
    $ch = curl_init(trim($url));

    if ($fail === FALSE) {
        $fp = fopen("/var/webdata/pages/$jmenosouboru", "w");
    } else {
        $fp = fopen("/var/webdata/pages/failed/$jmenosouboru", "w");
    }
    if (!$fp) {
        echo "Nepodarilo se vytvorit soubor. Jsou nastavena pristupova prava?";
        return FALSE;
    }
    if (!$ch) {
        echo "Nepodarilo se spojit se serverem.";
        return FALSE;
    }
    if ($fp && $ch) {
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0");
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $jmenosouboru;
    } else {
        return FALSE;
    }
}

function fetch_xml($xml_path, $searched, $owner_deal_id) {
    $loadfile = @simplexml_load_file("/var/webdata/feeds/$xml_path");
    if ($loadfile) {
        foreach ($loadfile->xpath("//DEAL") as $sleva) {
            if ((int) $sleva->ID == (int) $owner_deal_id) {
                if (strpos($searched, '->') === FALSE) {
                    if ((bool) $sleva->$searched != "") {
                        return((string) $sleva->$searched);
                    }
                } else {
                    $searched_elements = explode('->', $searched);
                    $a = $searched_elements[0];
                    $b = $searched_elements[1];
                    return((string) $sleva->$a->$b);
                }
            }
        }
    }
    return(FALSE);
}
