<?php

include_once 'connect.php';

$deal_metas = [];
$sphs = [];
$timestamp = date("Y-m-d H:i:s", Time());

$MetaDealsSQL = mysql_query("SELECT * FROM SlevyMeta");
$pocetMetaDeals = mysql_num_rows($MetaDealsSQL);
if ($pocetMetaDeals > 0) {

    while ($mt = mysql_fetch_array($MetaDealsSQL)) :

        $deal_metas[$mt['DealId']] = [
            'Discovered' => $mt['Discovered'],
            'Sph' => $mt['Sph'],
            'PrevSph' => $mt['PrevSph'],
            'Ssf' => $mt['Ssf'],
            'StartingSales' => $mt['StartingSales'],
        ];
    endwhile;
}


$MainDealsSQL = mysql_query("SELECT DealId, Customers FROM Slevy WHERE Status = 2");
$pocetMainDeals = mysql_num_rows($MainDealsSQL);
if ($pocetMainDeals > 0) {

    while ($md = mysql_fetch_array($MainDealsSQL)) :

        if (array_key_exists($md['DealId'], $deal_metas)) {

            //echo "DealId exist \n";
            $sib = $md['Customers'] - $deal_metas[$md['DealId']]['StartingSales'];
            $lifetime = time() - strtotime($deal_metas[$md['DealId']]['Discovered']);
            $hours = $lifetime / 3600;
            $sph = round($sib / $hours, 2);

            $deal_metas[$md['DealId']]['PrevSph'] = $deal_metas[$md['DealId']]['Sph'];
            $deal_metas[$md['DealId']]['Sph'] = $sph;
            $deal_metas[$md['DealId']]['Ssf'] = $sib;

            if ($sph > 0) {
                $sphs[$md['DealId']] = $sph;
            }
        } else {

            //echo "DealId NOT exist \n";
            mysql_query("
              INSERT INTO SlevyMeta
              (DealId, Discovered, Sph, PrevSph, Ssf, StartingSales)
              VALUES (
              " . $md['DealId'] . ",
              '" . $timestamp . "',
              0,
              0,
              0,
              " . $md['Customers'] . "
              )
              ") or die("Chyba insertu metaifo: " . mysql_error() . "\n");
        }
    endwhile;

    $sph_cnt = count($sphs);
    $sphs_sum = 0;
    
    foreach ($sphs as $sph) {
        $sphs_sum = $sphs_sum + $sph;
    }
    
    $avg_sph = $sphs_sum / $sph_cnt;
    //echo "$avg_sph \n";

    foreach ($deal_metas as $deal_id => $deal_data) {

        if ($deal_data['Sph'] > 0 && isset($sphs[$deal_id])) {

            $one_percent = $avg_sph / 100;
            $base_rating = ($deal_data['Sph'] / $one_percent) / 10;
            $rating = $base_rating;
            if($rating > 10){
                $rating = 10;
            }
            mysql_query("UPDATE Slevy SET Rating = '$rating' WHERE DealId = $deal_id") or die("Chyba updatu Ratingu $m_did: " . mysql_error());
        }

        mysql_query("UPDATE SlevyMeta SET 
              Sph = " . $deal_data['Sph'] . ",
              PrevSph = " . $deal_data['PrevSph'] . ",
              Ssf = " . $deal_data['Ssf'] . "
              WHERE DealId = " . $deal_id) or die("Chyba updatu DealMeta " . $deal_id . ": " . mysql_error());
    }
}
?>