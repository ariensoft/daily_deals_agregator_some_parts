
<?php

mb_internal_encoding('utf-8');

include_once 'connect.php';

$categories = array();
$nofkw_match = array(5);

$kategorie_sql = mysql_query("SELECT * FROM Kategorie");
$pocet_kategorii = mysql_num_rows($kategorie_sql);
if ($pocet_kategorii > 0) {

    while ($kategorie_row = mysql_fetch_array($kategorie_sql)) :
        $kws = array();
        $fkws = array();
        if (!empty($kategorie_row ["kw"])) {
            $kws = explode(",", $kategorie_row ["kw"]);
        }
        if (!empty($kategorie_row ["feed_kw"])) {
            $fkws = explode(",", $kategorie_row ["feed_kw"]);
        }
        $categories[$kategorie_row ["id"]] = array(
            'id' => $kategorie_row ["id"],
            'parent' => $kategorie_row ["rodic"],
            'name' => $kategorie_row ["jmeno"],
            'kws' => $kws,
            'fkws' => $fkws,
        );
    endwhile;
}

foreach ($categories as $kategorie_key => $kategorie_value) {
    $categories[$kategorie_key]['parrents'] = find_parrents($kategorie_key, $categories);
}

$cat_tree = buildTree($categories);
$all_kws = [];
kw_prepare(0, $cat_tree, $all_kws);

$hledej = mysql_query("SELECT Slevy.*, SlevyMeta.Perex as Perex FROM `Slevy` LEFT JOIN SlevyMeta ON Slevy.DealId = SlevyMeta.DealId WHERE Slevy.Status IN(1) ORDER BY Text");
//$hledej = mysql_query("SELECT Slevy.*, SlevyMeta.Perex as Perex FROM `Slevy` LEFT JOIN SlevyMeta ON Slevy.DealId = SlevyMeta.DealId WHERE Slevy.DealId = 12103 ORDER BY Text DESC");
$radku = mysql_num_rows($hledej);

if ($radku > 0 && !empty($categories)) {

    $prev_text = '';
    $prev_cat = 0;
    $prev_sub_cat = 0;
    $prev_tags = '';

    while ($nalezeno = mysql_fetch_array($hledej)) :
        $did = $nalezeno ["DealId"];
        $fulltext = $nalezeno ["Text"] . " " . $nalezeno ["Perex"];
        $title = $nalezeno ["Text"];
        $feed_text = $nalezeno ["FeedKws"];
        $server_id = $nalezeno ["ServerId"];
        $category = 0;
        $deal_categories = [];

        mysql_query("DELETE FROM `ms_import`.`SlevyKategorie` WHERE `SlevyKategorie`.`sleva_id` = $did");

        $fkws_result = search('fkws', $feed_text, $all_kws);
        $kws_result = search('kws', $fulltext, $all_kws);
        $branch_search_for = [];

        if ($fkws_result['cat'] != 0 || $kws_result['cat'] != 0) {
            if ($fkws_result['cat'] == $kws_result['cat']) {
                $category = $kws_result['cat'];
                $branch_search_for = array_merge($fkws_result['found'], $kws_result['found']);
            }
            if ($fkws_result['cat'] > 0 && $kws_result['cat'] == 0) {
                $category = $fkws_result['cat'];
                $branch_search_for = $fkws_result['found'];
            }
            if ($kws_result['cat'] > 0 && $fkws_result['cat'] == 0) {
                $category = $kws_result['cat'];
                $branch_search_for = $kws_result['found'];
            }
            
            if ($category !== 0) {
                $branch = [];
                foreach ($cat_tree as $cat_branch) {
                    if ($cat_branch['id'] == $category) {
                        $branch[] = $cat_branch;
                    }
                }
                $branch_results = [];
                branch_search($branch_search_for, $branch, $branch_results);
                $deal_categories = search_branch_results($branch_results);
                foreach ($deal_categories as $deal_category) {
                    mysql_query("INSERT INTO `ms_import`.`SlevyKategorie` (`sleva_id` , `kategorie_id` , `jmeno`) VALUES ( '$did', '" . $deal_category . "', '" . $categories[$deal_category]['name'] . "')") or die("chyba insertu kategorie: " . mysql_error());
                }
            
            }
        }
        mysql_query("UPDATE `Slevy` SET `Status` = 2 WHERE `DealId` = $did");
        unset($fkws_result);
        unset($kws_result);
    endwhile;
}

function search_branch_results($branch_results) {
    $categories = [];
    if(!empty($branch_results)){
        $levels = [];
        foreach ($branch_results as $branch_result_values) {
            $levels[] = $branch_result_values[0]['level'];
        }
        rsort($levels);
        $highest_level = $levels[0];
        $count_results = [];
        foreach ($branch_results as $branch_results_key => $branch_results_arr) {
            if($branch_results_arr[0]['level'] < $highest_level){
                unset($branch_results[$branch_results_key]);
            }else{
                $count_results[$branch_results_key] = count($branch_results_arr);
            }
        }
        arsort($count_results);
        $keys = array_keys($count_results);
        foreach ($branch_results as $key => $values) {
            if($key == $keys[0]){
                $categories = $values[0]['parrents'];
            }
        }
        array_unshift($categories, $keys[0]);
        krsort($categories);

    }
    return($categories);
}

function branch_search($branch_search_for, $branch, &$results, $level = 0) {
    foreach ($branch as $leaf) {
        $search_in = array_merge($leaf['fkws'], $leaf['kws']);
        if(!empty($search_in)){
            foreach ($branch_search_for as $search_for) {
                if(in_array($search_for, $search_in)){
                    $results[$leaf['id']][] = ['id' => $leaf['id'], 'parrents' => $leaf['parrents'], 'found' => $search_for, 'level' => $level];
                }
            }
        }
        if(!empty($leaf['childs'])){
            branch_search($branch_search_for, $leaf['childs'], $results, $level + 1);
        }
    }
}

function search($mode, $text, $all_kws) {
    $results = [];
    $return['cat'] = 0;
    foreach ($all_kws as $cat_id => $keywords) {
        $kw_search = kw_operate($text, $keywords[$mode], $cat_id);
        if ($kw_search !== FALSE) {
            $results[$cat_id] = $kw_search;
        }
    }
    if (!empty($results)) {

        $scores = [];
        foreach ($results as $result_id => $found_kws) {
            $score = 0;
            foreach ($found_kws as $found_kw) {
                $score = ($score + $found_kw['hits']) + (1 / $found_kw['first_occurence']);
            }
            $scores[$result_id] = $score;
        }
        arsort($scores);
        $keys = array_keys($scores);
        $return['cat'] = $keys[0];
        foreach ($results as $res_id => $res_found) {
            if ($res_id == $keys[0]) {
                $hits = [];
                foreach ($res_found as $word => $values) {
                    $hits[] = $word;
                }
                $return['found'] = $hits;
            }
        }
    }
    return($return);
}

function kw_prepare($cat_id, $cat_tree, &$all_kws, $level = 0) {

    foreach ($cat_tree as $cat) {
        if ($level == 0) {
            $cat_id = $cat['id'];
            $all_kws[$cat_id]['kws'] = $cat['kws'];
            $all_kws[$cat_id]['fkws'] = $cat['fkws'];
        } else {
            $all_kws[$cat_id]['kws'] = array_merge($all_kws[$cat_id]['kws'], $cat['kws']);
            $all_kws[$cat_id]['fkws'] = array_merge($all_kws[$cat_id]['fkws'], $cat['fkws']);
        }

        if (isset($cat['childs']) && !empty($cat['childs'])) {
            kw_prepare($cat_id, $cat['childs'], $all_kws, $level + 1);
        }
    }
}

function kw_operate($txt, $kw_arr, $cat_id) {

    $clean_text = text_cleaner(mb_strtolower($txt));
    $found = [];
    $return = [];
    foreach ($kw_arr as $kw) {

        $keywords = preg_split("/[\s,]+/", mb_strtolower($kw));
        $keywords_count = count($keywords);

        for ($i = 0; $i <= $keywords_count - 1; $i++) {
            $trimmed_kw = preg_replace('/\s+/', '', $keywords[$i]);

            if (mb_strlen($trimmed_kw) > 0) {
                if ($i == 0) {
                    if ($trimmed_kw === "+") {
                        $regexp = "\b\d+\s*\b";
                    } elseif (strpos($trimmed_kw, "_") !== false) {
                        $exact_word = str_replace("_", "", $trimmed_kw);
                        $regexp = "\b$exact_word\b";
                    } elseif (strpos($trimmed_kw, "+") !== false && mb_strlen($trimmed_kw) > 1) {
                        $exact_word = str_replace("+", "", $trimmed_kw);
                        $regexp = "\b\d+$exact_word\w*\b";
                    } else {
                        $regexp = "\b$trimmed_kw\w*\b";
                    }
                } else {
                    $regexp = $regexp . "\s\b$trimmed_kw\w*\b";
                }
            }
        }

        $hits = [];
        preg_match_all("/$regexp/iu", $clean_text, $hits);
        $matches = count($hits[0]);

        if ($matches > 0) {
            foreach ($hits as $hit) {
                $pos = mb_strpos($clean_text, $hit[0]);
                $found[$kw] = ['first_occurence' => $pos + 1, 'hits' => $matches];
            }
        }
    }

    if (count($found) > 0) {
        asort($found);
        return($return[$cat_id] = $found);
    }
    return FALSE;
}

function buildTree($items) {

    $childs = [];

    foreach ($items as &$item)
        $childs[$item['parent']][] = &$item;
    unset($item);

    foreach ($items as &$item)
        if (isset($childs[$item['id']]))
            $item['childs'] = $childs[$item['id']];

    return $childs[0];
}

function find_parrents($id, $categories) {
    $path = [];
    $parrent = $categories[$id]['parent'];
    $iter = 0;

    while ($parrent > 0) {
        $path[] = $parrent;
        $parrent = $categories[$parrent]['parent'];

        if ($iter >= 10) {
            break;
        }
        $iter++;
    }
    return($path);
}

function text_cleaner($text) {
    $strip = array(
        '"',
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
        ",",
        "<",
        ".",
        ">",
        "/",
        "?"
    );

    $text = trim(str_replace($strip, "", strip_tags($text)));

    return($text);
}
