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

//$hledej = mysql_query("SELECT Slevy.*, SlevyMeta.Perex as Perex FROM `Slevy` LEFT JOIN SlevyMeta ON Slevy.DealId = SlevyMeta.DealId WHERE Slevy.Status IN(1,2) ORDER BY Text");
$hledej = mysql_query("SELECT Slevy.*, SlevyMeta.Perex as Perex FROM `Slevy` LEFT JOIN SlevyMeta ON Slevy.DealId = SlevyMeta.DealId WHERE Slevy.DealId = 12103 ORDER BY Text DESC");
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

        //mysql_query("DELETE FROM `ms_import`.`SlevyKategorie` WHERE `SlevyKategorie`.`sleva_id` = $did");

        $fkw_results = search_classic($did, 'kws', $fulltext, $categories);
        if (!empty($fkw_results)) {
            $complete_fkw_results = add_missing_parrents($fkw_results, $categories);
            $fkw_results_tree = buildTree($complete_fkw_results);
            $fkw_scores = [];
            $fkw_categories = [];
            compute_score($did, $fkw_results_tree, $fkw_scores);
            if (!empty($fkw_scores)) {
                arsort($fkw_scores);
                $keys = array_keys($fkw_scores);
                foreach ($fkw_results_tree as $fkw_results_id => $fkw_results) {
                    if ($fkw_results['id'] == $keys[0]) {
                        $fkw_categories[] = $fkw_results_tree[$fkw_results_id];
                    }
                }
                print_r($fkw_categories);
                //save_deal_categories($did, $fkw_categories);
            }
            unset($fkw_categories);
            unset($complete_fkw_results);
            unset($fkw_results_tree);
            unset($fkw_scores);
        }
        unset($fkw_results);

    endwhile;
}

function save_deal_categories($did, $final_category_tree) {

    foreach ($final_category_tree as $cat) {
        mysql_query("INSERT INTO `ms_import`.`SlevyKategorie` (`sleva_id` , `kategorie_id` , `jmeno`) VALUES ( '$did', '" . $cat['id'] . "', '" . $cat['name'] . "')") or die("chyba insertu kategorie: " . mysql_error());
        if (isset($cat['childs']) && !empty($cat['childs'])) {
            save_deal_categories($did, $cat['childs']);
        }
    }
}

function compute_score($did, $results_tree, &$results, $level = 0, $score = 0, $current = 0) {
    foreach ($results_tree as $leaf) {
        if ($level == 0) {
            $score = 0;
            $current = $leaf['id'];
        }
        if (!empty($leaf['found'])) {
            foreach ($leaf['found'] as $found_kw => $kw_stats) {
                //echo $found_kw.' hits> '.$kw_stats['hits'].' '.$kw_stats['first_occurence']."\n";
                $score = ($score + $kw_stats['hits']) + (1 / $kw_stats['first_occurence']);
            }
        }
        //echo $leaf['name'] . " " . $score . "\n";
        if (isset($leaf['childs']) && !empty($leaf['childs'])) {
            compute_score($did, $leaf['childs'], $results, $level + 1, $score, $current);
        } else {

            //echo "break $current - $score \n\n";
            $results[$current] = $score;
        }
    }
}

function search_classic($did, $search_for, $text, $categories) {
    $search_results = [];
    //echo "$text \n";
    foreach ($categories as $category_id => $category_data) {
        $kw_search = kw_operate($text, $category_data[$search_for], $category_id);
        if ($kw_search !== FALSE) {
            $search_results[$category_id] = $category_data;
            $search_results[$category_id]['found'] = $kw_search;
        }
    }
    return($search_results);
}

function search($did, $search_for, $text, $server_id, $cat_tree, &$found, $level = 1) {
    $next_work = [];

    foreach ($cat_tree as $category) {
        if (count($category[$search_for] > 0) && mb_strlen($text) > 1) {
            $search_results = kw_operate($text, $category[$search_for], $category['id']);
            if ($search_results !== FALSE) {
                $found[$category['id']] = ['found' => $search_results];
                if (isset($category['childs']) && !empty($category['childs'])) {
                    $next_work = array_merge($next_work, $category['childs']);
                }
            }
        }
    }

    if (!empty($next_work)) {
        search($did, $search_for, $text, $server_id, $next_work, $found, $level + 1);
    } else {
        if (!empty($cat_tree)) {
            foreach ($cat_tree as $categories) {
                if (isset($categories['childs']) && !empty($categories['childs'])) {
                    $next_work = array_merge($next_work, $categories['childs']);
                }
            }
        }
        if (!empty($next_work)) {
            search($did, $search_for, $text, $server_id, $next_work, $found, $level + 1);
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
                $found[$hit[0]] = ['first_occurence' => $pos + 1, 'hits' => $matches];
            }
        }
    }

    if (count($found) > 0) {
        asort($found);
        return($return[$cat_id] = $found);
    }
    return FALSE;
}

function add_missing_parrents($results, $categories) {
    $existing_parrents = [];
    foreach ($results as $result) {
        $existing_parrents[] = $result['id'];
    }
    foreach ($results as $results_cat_id => $results_data) {
        foreach ($results_data['parrents'] as $parrent) {
            if (!in_array($parrent, $existing_parrents)) {
                $results[$parrent] = $categories[$parrent];
                $existing_parrents[] = $parrent;
            }
        }
    }
    return($results);
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
