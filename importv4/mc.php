<?php

mb_internal_encoding('utf-8');

include_once 'connect.php';

$kategorie = array();
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
        $kategorie[$kategorie_row ["id"]] = array(
            'id' => $kategorie_row ["id"],
            'parent' => $kategorie_row ["rodic"],
            'name' => $kategorie_row ["jmeno"],
            'kws' => $kws,
            'fkws' => $fkws,
        );
    endwhile;
}

$cat_tree = buildTree($kategorie);

$hledej = mysql_query("SELECT Slevy.*, SlevyMeta.Perex as Perex FROM `Slevy` LEFT JOIN SlevyMeta ON Slevy.DealId = SlevyMeta.DealId WHERE Slevy.Status IN(1,2) ORDER BY Text DESC");
$radku = mysql_num_rows($hledej);

if ($radku > 0) {

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

        mysql_query("DELETE FROM `ms_import`.`SlevyKategorie` WHERE `SlevyKategorie`.`sleva_id` = $did");

        $deal_cats = [];
        iterate_parrents($feed_text.$fulltext, $server_id, $nofkw_match, $cat_tree, $deal_cats);
        if (!empty($deal_cats)) {
            krsort($deal_cats);
            $keys = array_keys($deal_cats);
            $last_leaf = $deal_cats[$keys[0]];
            $deal_categories = find_parrents($last_leaf, $kategorie);
            if (!empty($deal_categories)) {
                foreach ($deal_categories as $deal_category) {
                    $n = $kategorie[$deal_category]['name'];
                    mysql_query("INSERT INTO `ms_import`.`SlevyKategorie` (`sleva_id` , `kategorie_id` , `jmeno`) VALUES ( '$did', '$deal_category', '$n')") or die("chyba insertu tagu: " . mysql_error());
                }
            }
        }
        unset($deal_cats);
        mysql_query("UPDATE `Slevy` SET `Status` = 2 WHERE `DealId` = $did");
    endwhile;
}

function iterate_parrents($text, $server_id, $nofkw_match, $cat_tree, &$deal_cats, $level = 0) {
    $childs = array();
    $results = [];
    foreach ($cat_tree as $ve) {
        if (in_array($server_id, $nofkw_match)) {
            $fkws_kws = $ve['kws'];
        } else {
            $fkws_kws = array_merge($ve['kws'], $ve['fkws']);
        }
        if (count($fkws_kws) > 0 && mb_strlen($text) > 1) {
            $test = kw_operate($text, $fkws_kws, $ve['id']);
            if ($test !== FALSE) {
                $results[$ve['id']] = $test;
            }
        }
        $vechilds = getChildrenFor($cat_tree, $ve['id']);
        if ($vechilds !== FALSE) {
            $childs = array_merge($childs, $vechilds);
        }
    }
    if (count($results) > 0) {
        $deal_cats[$level] = compute_category($results);
        if ($deal_cats[$level] !== 0) {
            $childs = getChildrenFor($cat_tree, $deal_cats[$level]);
        }
    }
    if (!empty($childs)) {
        iterate_parrents($text, $server_id, $nofkw_match, $childs, $deal_cats, $level + 1);
    }
}

function find_childs($cid, $arr, &$res) {

    foreach ($arr as $k => $v) {
        if ($v['parent'] == $cid) {
            $res[] = $v['id'];
        }
        if (isset($v['childs'])) {
            find_childs($cid, $v['childs'], $res);
        }
    }
}

function find_parrents($id, $categories) {
    $path[] = $id;
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

function getChildrenFor($ary, $id) {
    $results = array();

    foreach ($ary as $el) {
        if ($el['parent'] == $id) {
            $results[] = $el;
        }
        if (isset($el['childs']) && ($children = getChildrenFor($el['childs'], $id)) !== FALSE) {
            $results = array_merge($results, $children);
        }
    }

    return count($results) > 0 ? $results : FALSE;
}

function compute_category($results) {
    $cat = 0;
    if (count($results) > 0) {
        $counter = [];

        foreach ($results as $result => $data) {
            $counter[$result] = count($data);
        }
        arsort($counter);
        $keys = array_keys($counter);
        $cat = $keys[0];
        if (count($counter) > 1) {
            if ($counter[$keys[0]] == $counter[$keys[1]]) {
                $pos_counter = [];
                for ($i = 0; $i < 2; $i++) {
                    $words = count($results[$keys[$i]]);
                    $word_pos_sum = 0;
                    foreach ($results[$keys[$i]] as $r) {
                        $word_pos_sum = $word_pos_sum + $r;
                    }
                    $pos_counter[$keys[$i]] = $word_pos_sum / $words;
                }
                asort($pos_counter);
                $pos_counter_keys = array_keys($pos_counter);
                $cat = $pos_counter_keys[0];
            }
        }
    }
    return($cat);
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
                $found[$hit[0]] = $pos;
            }
        }
    }

    if (count($found) > 0) {
        asort($found);
        return($return[$cat_id] = $found);
    }
    return FALSE;
}

function iterate($arr, $path = array(), $level = 0) {

    foreach ($arr as $k => $v) {
        $path[$level] = $v['name'];
        if (isset($v['childs'])) {
            iterate($v['childs'], $path, $level + 1);
        } else {
            echo texy($level) . $v['name'] . " $level \n";
            print_r($path);
        }
    }
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

function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000)) - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}
