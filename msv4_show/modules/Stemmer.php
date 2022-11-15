<?php

namespace app\modules;

mb_internal_encoding('utf-8');

class Stemmer extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\controllers';

    public function init() {
        parent::init();

        // custom initialization code goes here
    }

    public function cz_stem($word, $aggressive = FALSE) {
        $nepatricne = array(
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
            ",",
            "<",
            ".",
            ">",
            "/",
            "?"
        );
        $s = str_replace($nepatricne, '', $word);
        $s = mb_strtolower($s, 'UTF-8');
        $words = preg_split("/[\s,]+/", $s);
        $stems = array();
        $stop_stem = array("hodink", "mask", "tašk", "nabíječk", "šperk", "žehličk", "tunik", "fotk", "tenisk", "kamínk", "baňk", "budík", "buňk", "disk");
        $versions = array();

        foreach ($words as $w) {
            if (!preg_match("/^\d+$/", $w)) {

                $versions[0] = $this->remove_case($w);
                $versions[1] = $this->remove_possessives($versions[0]);
                $versions[2] = $this->remove_comparative($versions[1]);
                $versions[3] = $this->remove_diminutive($versions[2]);
                $i = 0;

                foreach ($versions as $value) {

                    if (in_array($value, $stop_stem) && $i - 1 >= 0) {
                        $w = $versions[$i - 1];
                        break;
                    } else {
                        $w = $value;
                    }

                    $i++;
                }
                if ($aggressive === TRUE) {
                    $w = $this->remove_augmentative($w);
                    $w = $this->remove_derivational($w);
                }
            }

            $stems[] = $w;
        }
        if (!empty($stems)) {
            $stemmed = implode(" ", $stems);
            return $stemmed;
        } else {
            return $word;
        }
    }

    function remove_case($word) {

        $len = mb_strlen($word);
        if ($len > 7 && in_array(mb_substr($word, -5), array("atech"))) {
            return mb_substr($word, 0, -5);
        }
        if ($len > 6) {
            if (in_array(mb_substr($word, -4), array("ětem"))) {
                return $this->palatalise(mb_substr($word, 0, -3));
            }
            if (in_array(mb_substr($word, -4), array("atům"))) {
                return mb_substr($word, 0, -4);
            }
            if (in_array(mb_substr($word, -4), array("etem"))) {
                return mb_substr($word, 0, -4);
            }
        }
        if ($len > 5) {
            if (in_array(mb_substr($word, -3), array("ech", "ich", "ích", "ého", "ěmi", "emi", "ému", "ete", "eti", "iho", "ího", "ími", "imu"))) {
                return $this->palatalise(mb_substr($word, 0, -2));
            }
            if (in_array(mb_substr($word, -3), array("ách", "ata", "aty", "ých", "ama", "ami", "ové", "ovi", "ými"))) {
                return mb_substr($word, 0, -3);
            }
        }
        if ($len > 4) {
            if (in_array(mb_substr($word, -2), array("em"))) {
                return $this->palatalise(mb_substr($word, 0, -1));
            }
            if (in_array(mb_substr($word, -2), array("es", "ém", "ím"))) {
                return $this->palatalise(mb_substr($word, 0, -2));
            }
            if (in_array(mb_substr($word, -2), array("ům", "at", "ám", "os", "us", "ým", "mi", "ou"))) {
                return mb_substr($word, 0, -2);
            }
        }
        if ($len > 3) {
            if (in_array(mb_substr($word, -1), array("e", "i", "í", "ě"))) {
                return $this->palatalise($word);
            }
            if (in_array(mb_substr($word, -1), array("u", "y", "ů", "a", "o", "á", "é", "ý", "ť"))) {
                return mb_substr($word, 0, -1);
            }
        }
        return $word;
    }

    function remove_possessives($word) {
        if (mb_strlen($word) > 5) {
            if (in_array(mb_substr($word, -2), array("ov", "ův"))) {
                return mb_substr($word, 0, -2);
            }
            if (in_array(mb_substr($word, -2), array("in"))) {
                return $this->palatalise(mb_substr($word, 0, -1));
            }
        }
        return $word;
    }

    function remove_comparative($word) {
        if (mb_strlen($word) > 5) {
            if (in_array(mb_substr($word, -3), array("ejš", "ějš"))) {
                return $this->palatalise(mb_substr($word, 0, -2));
            }
        }
        return $word;
    }

    function remove_diminutive($word) {
        $len = mb_strlen($word);
        if ($len > 7 && in_array(mb_substr($word, -5), array("oušek"))) {
            return mb_substr($word, 0, -5);
        }
        if ($len > 6) {
            if (in_array(mb_substr($word, -4), array("eček", "éček", "iček", "íček", "enek", "ének", "inek", "ínek"))) {
                return $this->palatalise(mb_substr($word, 0, -3));
            }
            if (in_array(mb_substr($word, -4), array("áček", "aček", "oček", "uček", "anek", "onek", "unek", "ánek"))) {
                return $this->palatalise(mb_substr($word, 0, -4));
            }
        }
        if ($len > 5) {
            if (in_array(mb_substr($word, -3), array("ečk", "éčk", "ičk", "íčk", "enk", "énk", "ink", "ínk"))) {
                return $this->palatalise(mb_substr($word, 0, -2)); //orig -3 ale bundička = bun
            }
            if (in_array(mb_substr($word, -3), array("áčk", "ačk", "očk", "učk", "ank", "onk", "unk", "átk", "ánk", "ušk"))) {
                return mb_substr($word, 0, -3);
            }
        }
        if ($len > 4) {
            if (in_array(mb_substr($word, -2), array("ek", "ék", "ík", "ik", "ěk"))) {
                return $this->palatalise(mb_substr($word, 0, -1));
            }
            if (in_array(mb_substr($word, -2), array("ák", "ak", "ok", "uk"))) {
                return mb_substr($word, 0, -1);
            }
        }
        if ($len > 3 && mb_substr($word, -1) == "k") {
            return mb_substr($word, 0, -1);
        }
        return $word;
    }

    function remove_augmentative($word) {
        $len = mb_strlen($word);
        if ($len > 6 && in_array(mb_substr($word, -4), array("ajzn"))) {
            return mb_substr($word, 0, -4);
        }
        if ($len > 5 && in_array(mb_substr($word, -3), array("izn", "isk"))) {
            return $this->palatalise(mb_substr($word, 0, -2));
        }
        if ($len > 4 && in_array(mb_substr($word, -2), array("ák"))) {
            return mb_substr($word, 0, -2);
        }
        return $word;
    }

    function remove_derivational($word) {
        $len = mb_strlen($word);
        if ($len > 8 && in_array(mb_substr($word, -6), array("obinec"))) {
            return mb_substr($word, 0, -6);
        }
        if ($len > 7) {
            if (in_array(mb_substr($word, -5), array("ionář"))) {
                return $this->palatalise(mb_substr($word, 0, -4));
            }
            if (in_array(mb_substr($word, -5), array("ovisk", "ovstv", "ovišt", "ovník"))) {
                return mb_substr($word, 0, -5);
            }
        }
        if ($len > 6) {
            if (in_array(mb_substr($word, -4), array("ásek", "loun", "nost", "teln", "ovec", "ovík", "ovtv", "ovin", "štin"))) {
                return mb_substr($word, 0, -4);
            }
            if (in_array(mb_substr($word, -4), array("enic", "inec", "itel"))) {
                return $this->palatalise(mb_substr($word, 0, -3));
            }
        }
        if ($len > 5) {
            if (in_array(mb_substr($word, -3), array("árn"))) {
                return mb_substr($word, 0, -3);
            }
            if (in_array(mb_substr($word, -3), array("ěnk", "ián", "ist", "isk", "išt", "itb", "írn"))) {
                return $this->palatalise(mb_substr($word, 0, -2));
            }
            if (in_array(mb_substr($word, -3), array("och", "ost", "ovn", "oun", "out", "ouš", "ušk", "kyn", "čan", "kář", "néř", "ník", "ctv", "stv"))) {
                return mb_substr($word, 0, -3);
            }
        }
        if ($len > 4) {
            if (in_array(mb_substr($word, -2), array("áč", "ač", "án", "an", "ář", "as"))) {
                return mb_substr($word, 0, -2);
            }
            if (in_array(mb_substr($word, -2), array("ec", "en", "ěn", "éř", "íř", "ic", "in", "ín", "it", "iv", "el"))) {
                return $this->palatalise(mb_substr($word, 0, -1));
            }
            if (in_array(mb_substr($word, -2), array("ob", "ot", "ov", "oň", "ul", "yn", "čk", "čn", "dl", "nk", "tv", "tk", "vk"))) {
                return mb_substr($word, 0, -2);
            }
        }
        if ($len > 3 && in_array(mb_substr($word, -1), array("c", "č", "k", "l", "n", "t"))) {
            return mb_substr($word, 0, -1);
        }

        return $word;
    }

    function palatalise($word) {

        $len = mb_strlen($word);

        if (in_array(mb_substr($word, -2), array("zi", "ze", "ži", "že"))) {
            $word = mb_substr($word, 0, -1); //-2 . "h";
            return $word;
        }

        if (in_array(mb_substr($word, -2), array("ci", "ce", "či", "če"))) {
            $word = mb_substr($word, 0, -1); //-2."k";
            return $word;
        }
        if (in_array(mb_substr($word, -3), array("čtě", "čti", "čtí"))) {
            $word = mb_substr($word, 0, -3) . "ck";
            return $word;
        }
        if (in_array(mb_substr($word, -3), array("ště", "šti", "ští"))) {
            $word = mb_substr($word, 0, -3) . "sk";
            return $word;
        }
        if ($len > 7) {
            if (in_array(mb_substr($word, -3), array("oří"))) {
                $word = mb_substr($word, 0, -2) . 'r'; //Podkrušnohoří hor
                return $word;
            }
        }
        return mb_substr($word, 0, -1);
    }

}
