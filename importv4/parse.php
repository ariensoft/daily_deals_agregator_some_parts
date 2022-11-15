<?php

mb_internal_encoding('utf-8');

include_once 'stemmer.php';
include_once 'GetImage.php';
include_once 'simple_html_dom_15.php';
include_once 'connect.php';


$parserStatus = 1;
$statusSQL = mysql_query("SELECT Status FROM ParserStatus WHERE Id = 1");

$statusrows = mysql_num_rows($statusSQL);
if ($statusrows > 0) {
    while ($status = mysql_fetch_array($statusSQL)) :
        $parserStatus = $status['Status'];
    endwhile;
}

if ($parserStatus == 0) {

    mysql_query("UPDATE ParserStatus SET Status = 1  WHERE Id = 1") or die("chyba update: " . mysql_error());

    $dnesni_datum = Date("Y-m-d", Time());

    $LastInserted = 0;
    $HashesARR = array();
    $prev_inserted = array();

    $strip = array(
        "~",
        "`",
        "!",
        "@",
        "#",
        "$",
        "%",
        "^",
        "&",
        "*",
        "(",
        ")",
        "_",
        "=",
        "+",
        "[",
        "{",
        "]",
        "}",
        "\\",
        "|",
        ";",
        ":",
        "\"",
        "'",
        "&#8216;",
        "&#8217;",
        "&#8220;",
        "&#8221;",
        "&#8211;",
        "&#8212;",
        "â€”",
        "â€“",
        "<",
        ".",
        ">",
        "/",
        "?"
    );

    $filtrCR = array("Krkonoše", "Slovensko", "Zahraničí", "Pobyty - Cestování", "Pro Celou ČR", "Beskydy", "Jeseníky", "Chorvatsko", "Česká Republka", "Zbozi", "Vino", "DOVOLENÁ - ZÁJEZDY - CESTOVÁNÍ", "Celá ČR, Praha", "Celá ČR,Tábor", "Nezařazené", "Cestování", "Vše", "CR", "Čr", "Česká Republika", "čr", "ČR", "TPY Na Výlet", "Slaný; Praha; Brno; Ostrava", "TPY Na Výlet V ČR", ",čr", "SPECAL-DEAL", "Dovolená - cestování", "cr", "ČESKÁ REPUBLKA", "Česká Republika", "Cr", "cr", "ČR - Cestování", "Celá Republika", "Home Page", "Accueil", "Nicio", "Start", 'Česko', "česko", "Zboží", "zboží");

    $filtr = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0", "I", "II", "III", "(Písek)", "Další Nabídka", "další nabídka", "-město", "- Alkohol", "ČB");

    $slovaci = array('Bratislava', 'bratislava', 'Krása');

// Udelame si pole s potrebnymi udaji.
    $LastDealInsertedSQL = mysql_query("SELECT DealId FROM Slevy ORDER BY DealId DESC limit 1");
    while ($LastDealInserted = mysql_fetch_array($LastDealInsertedSQL)) {
        $poceti = mysql_num_rows($LastDealInsertedSQL);
        if ($poceti > 0) {
            $LastInserted = $LastDealInserted["DealId"];
        }
    }
    $jinycas = array("FajnSlevy.cz", "Bezvasleva.cz", "Zhave-Slevy.cz", "Slevoexpres.cz", "KrálovnaSlev.cz");

//Deal hashes
    $HashesSQL = mysql_query("SELECT DealId, Hash FROM Slevy");
    $poceth = mysql_num_rows($HashesSQL);
    if ($poceth > 0) {

        while ($Hashe = mysql_fetch_array($HashesSQL)):
            $HashId = $Hashe["DealId"];
            $Hashdb = $Hashe["Hash"];

            $HashesARR[$HashId] = $Hashdb;

        endwhile;
        reset($HashesARR);
    }

//Otevirame xml
    $ziskejadresu = mysql_query("SELECT * FROM Servers WHERE FeedType = 'skrz' ORDER BY ServerId ASC");

//$ziskejadresu = mysql_query("SELECT * FROM Servers WHERE ServerID = 209 ORDER BY Name ASC");
    while ($zaznam = mysql_fetch_array($ziskejadresu)):

        $ServerId = $zaznam["ServerId"];
        $FeedUrl = $zaznam["FeedUrl"];
        $ServerName = $zaznam["Name"];
        $jmenosouboru = $ServerId . ".xml";
        $priority = $zaznam["Priority"];
        $urladd = $zaznam["UrlAdd"];
        $element = $zaznam["Element"];

        $loadfile = @simplexml_load_file("/var/webdata/feeds/$jmenosouboru");

        if ($loadfile) {
            foreach ($loadfile->xpath("//DEAL") as $sleva) {
                $usefull_items = array();
                $cities = array();
                if (isset($sleva->CITY) or $sleva->CITIES) {
                    if ((bool) $sleva->CITY != "") {
                        $deal_city = (string) $sleva->CITY;
                        $cities = array($deal_city);
                    } else {
                        if (!empty($sleva->CITIES->CITY)) {
                            foreach ($sleva->CITIES->CITY as $x_city) {
                                $cities[] = (string) $x_city;
                            }
                        } else {
                            $cities = array("Celá čr");
                        }
                    }
                } else {
                    $cities = array("Celá čr");
                }

                reset($prev_inserted);
                $server = "$ServerName";
                $ownerId = $sleva->ID;
                $title = mysql_real_escape_string($sleva->TITLE);
                $marked = array();
                $stems = array();
                $stemed_text = cz_stem($title, FALSE);
                $stems = explode(" ", $stemed_text);
                foreach ($stems as $s) {
                    $marked[] = "M" . $s;
                }
                $search_text = implode(" ", $marked);

                $url = trim($sleva->URL);
                if ((bool) $sleva->IMAGE != "") {
                    $image_url = rawurldecode($sleva->IMAGE);
                } else {
                    $image_url = rawurldecode($sleva->IMAGES->IMAGE);
                }
                $finalprice = $sleva->FINAL_PRICE;
                $final_price = str_replace(",", "", $finalprice);
                $originalprice = $sleva->ORIGINAL_PRICE;
                $original_price = str_replace(",", "", $originalprice);
                $usetrite = $original_price - $final_price;
                if ($usetrite > 0) {
                    $discount = ((100 * $usetrite) / $original_price);
                } else {
                    $discount = 0;
                }
                $zacatek = date("Y-m-d", strtotime($sleva->DEAL_START));
                $den_ukonceni = date("Y-m-d", strtotime($sleva->DEAL_END));
                $start = date("Y-m-d H:i:s", strtotime($sleva->DEAL_START));
                $konec = date("Y-m-d H:i:s", strtotime($sleva->DEAL_END));
                $zakazniku = $sleva->CUSTOMERS;

                if (isset($sleva->TAGS->TAG) && !empty($sleva->TAGS->TAG)) {
                    foreach ($sleva->TAGS->TAG as $stag) {
                        $usefull_items[] = (string) $stag;
                    }
                }

                if (isset($sleva->TAG) && !empty($sleva->TAG)) {
                    $usefull_items[] = (string) $sleva->TAG;
                }

                if (isset($sleva->CATEGORIES->CATEGORY) && !empty($sleva->CATEGORIES->CATEGORY)) {
                    foreach ($sleva->CATEGORIES->CATEGORY as $stag) {
                        $usefull_items[] = (string) $stag;
                    }
                }

                if (isset($sleva->CATEGORY) && !empty($sleva->CATEGORY)) {
                    $usefull_items[] = (string) $sleva->CATEGORY;
                }

                if (isset($sleva->DEAL_TYPE) && !empty($sleva->DEAL_TYPE)) {
                    $usefull_items[] = (string) $sleva->DEAL_TYPE;
                }

                $usefull_items_db = mysql_real_escape_string(implode(" ", $usefull_items));

                $fiximage_url = $image_url;
                $originalImage = trim($fiximage_url);

                if ($zacatek <= $dnesni_datum && $den_ukonceni >= $dnesni_datum) {

                    $title = strip_tags($title);
                    $fixurl = rawurldecode($url);
                    $CreatorID = 1;
                    $SubcatID = 1;
                    $CategoryId = 1;
                    $image_address = "http://images.meslevy.cz/none.jpg";

                    $pieces = explode(" ", $title);
                    $PocetSlov = count($pieces);

                    if ($PocetSlov >= 2) { //Zacatek pieces
                        $AllDeal = '[' . $ServerId . '][' . $ownerId . ']';
                        $Hash = $AllDeal;

                        if (!in_array($Hash, $HashesARR)) {
                            $LastInserted++;
                            $HashesARR[] = $Hash;

                            $inarray = search_arr_index($title . $ServerId . $zacatek, $prev_inserted);

                            if ($inarray === FALSE) {

                                $longText = '';

                                $image = new GetImage;
                                $image->source = trim($fiximage_url);
                                $image->save_to = '/var/webdata/images/deals/';  // with trailing slash at the end
                                $image->custom_name = "$LastInserted";

                                $get = $image->download('gd'); // using GD

                                if ($get != false) {
                                    $image_address = "http://images.meslevy.cz/deals/" . $get;
                                }

                                $prev_inserted[$title . $ServerId . $zacatek] = array($image_address, $longText);
                            } else {
                                $image_address = $inarray[0];
                                $longText = $inarray[1];
                            }

                            mysql_query("INSERT INTO Slevy (Hash,ServerId,Text,TextFull,SearchText,FeedKws,FPrice,OPrice,Discount,DStart,DEnd,Url,Image,OriginalImage,Customers,Status )
                              VALUES ('$Hash','$ServerId','$title','$longText','$search_text','$usefull_items_db','$finalprice','$originalprice','$discount','$zacatek','$konec','$url','$image_address','$originalImage','$zakazniku','1') ") or die("Chyba insertu: " . mysql_error());

                            foreach ($cities as $city) {

                                if (!empty($city)) {

                                    $cityuc = ucwords($city);
                                    $cityt = trim($cityuc);
                                    $cityrep = str_replace($strip, ",", $cityt);
                                    $exp_cities = explode(",", $cityrep);

                                    foreach ($exp_cities as $exp_city) {
                                        if (in_array($exp_city, $filtrCR))
                                            $exp_city = "Celá ČR";
                                        $exp_city_r = str_replace($filtr, "", $exp_city);

                                        $city_search = mysql_real_escape_string($exp_city_r);
                                        $MestoSQL = mysql_query("SELECT CityId, Name FROM Cities WHERE `Name` LIKE '$city_search%' order by Residents DESC limit 1");
                                        $pocetc = mysql_num_rows($MestoSQL);
                                        if ($pocetc > 0) {

                                            while ($Mesto = mysql_fetch_array($MestoSQL)):

                                                $CityId = $Mesto["CityId"];
                                                $CityName = $Mesto["Name"];

                                            endwhile;
                                        }else {
                                            if (in_array($exp_city_r, $slovaci)) {
                                                $CityId = 6253;
                                                $CityName = 'Nezname';
                                            } else {
                                                $CityId = 6253;
                                                $CityName = 'Nezname';
                                                //die("Chyba city $city_search \n");
                                            }
                                        }
                                    }
                                } else {
                                    $CityId = 6253;
                                    $CityName = 'Nezname';
                                    //die("prazdne city \n");
                                }
                                mysql_query("INSERT INTO SlevyMesta (DealId, CityId, Name) VALUES ('$LastInserted', '$CityId', '$CityName')") or die("Chyba insertu mesta $LastInserted, $CityId, $CityName : " . mysql_error());
                            }
                        } else {

                            $DId = array_search($Hash, $HashesARR);
                            if ($DId) {
                                mysql_query("UPDATE Slevy SET Customers = '$zakazniku', DStart = '$zacatek', DEnd = '$konec', Discount = '$discount', FPrice = '$finalprice', OriginalImage = '$originalImage', Text = '$title' WHERE DealId = $DId") or die("Chyba updatu $DId: " . mysql_error());
                            } else {
                                die('chyba Did');
                            }
                        }
                        unset($AllDeal);
                        unset($Hash);
                        unset($pieces);
                        unset($newarr);
                    } //Konec pieces
                }
            }
            //unlink("/var/www/importv4/feeds/$jmenosouboru");
        } else {
            echo 'neni soubor' . $ServerId;
        }

    endwhile;


    unset($HashesARR);
    unset($LastInserted);
    unset($prev_inserted);

    //include '/var/www/importv4/ParseRajknih.php';
    //include '/var/www/importv4/ParseVenda.php';
    //include '/var/www/importv4/rater.php';

    mysql_query("UPDATE `Slevy` SET `Status` = 2 WHERE `DEnd` > NOW() AND `Status` NOT IN (0,1)");
    mysql_query("UPDATE `Slevy` SET `Status` = 3 WHERE `DEnd` < NOW() AND `Status` != 0");
    //include '/var/www/import/duplicity.php';

    mysql_query("UPDATE ParserStatus SET Status = 0  WHERE Id = 1") or die("chyba update: " . mysql_error());
} else {
    mysql_query("UPDATE `Slevy` SET `Status` = 3 WHERE `DEnd` < NOW() AND `Status` != 0)");
    echo "parser is already running \n";
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
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return $jmenosouboru;
    } else {
        return FALSE;
    }
}

function search_arr_index($what, $array) {
    $return = array();
    foreach ($array as $keys => $values) {
        if ($keys === $what) {
            foreach ($values as $value) {
                $return[] = $value;
            }
            return $return;
        }
    }
    return FALSE;
}

?>
