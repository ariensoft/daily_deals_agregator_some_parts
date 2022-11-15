<?php

use yii\helpers\Html;
use app\models\Kategorie;

echo "<MS>\n";
if (!empty($data)) {
    $cats = Kategorie::find()->asArray()->all();
    $categories = [];
    foreach ($cats as $cat) {
        $categories[$cat ["id"]] = array(
            'id' => $cat ["id"],
            'parent' => $cat ["rodic"],
            'name' => $cat ["jmeno"],
        );
    }
    foreach ($data as $item) {
        echo "<DEAL>\n";
        echo "<DEAL_ID>" . Html::encode($item->DealId) . "</DEAL_ID>\n";
        echo "<SERVER>" . Html::encode($item->server->Name) . "</SERVER>\n";
        echo "<TITLE>" . Html::encode($item->Text) . "</TITLE>\n";
        if(!empty($item->server->UrlAddBrno)){
        $commisionPart = urldecode($item->server->UrlAddBrno);
        }else{
            $commisionPart = urldecode($item->server->UrlAdd);
        }
        $urlPart = urldecode($item->Url);

        switch ($item->server->AddType) {
            case 3:
                //custom
                $url = $item->Url;
                break;

            case 2:
                //za
                $url = $urlPart . $commisionPart;
                break;

            case 1:
                //pred
                $url = $commisionPart . $urlPart;
                break;

            default:
                $url = $item->Url;
        }
        echo "<URL>" . Html::encode($url) . "</URL>\n";
        echo "<IMAGE_URL>" . Html::encode($item->OriginalImage) . "</IMAGE_URL>\n";
        echo "<FINAL_PRICE>" . Html::encode($item->FPrice) . "</FINAL_PRICE>\n";
        echo "<ORIGINAL_PRICE>" . Html::encode($item->OPrice) . "</ORIGINAL_PRICE>\n";
        echo "<DISCOUNT>" . Html::encode($item->Discount) . "</DISCOUNT>\n";
        echo "<SAVINGS>" . Html::encode((int) $item->OPrice - (int) $item->FPrice) . "</SAVINGS>\n";
        echo "<DEAL_START>" . Html::encode($item->DStart) . "</DEAL_START>\n";
        echo "<DEAL_END>" . Html::encode($item->DEnd) . "</DEAL_END>\n";
        echo "<CUSTOMERS>" . $item->Customers . "</CUSTOMERS>\n";

        $deal_categories = [];
        $cat_tree = [];
        $catys = [];
        if (count($item->categories) > 0) {
            foreach ($item->categories as $category) {
                foreach ($categories as $key_categories => $value_categories) {
                    if ((int) $category->id == $value_categories['id']) {
                        $deal_categories[$value_categories['id']] = $value_categories;
                    }
                }
            }
            $cat_tree = Kategorie::buildTree($deal_categories);

            $catys = Kategorie::tree_to_arr($cat_tree, $cat_tree[0]['id']);
            $c = [];
            foreach ($catys as $caty) {
                $c[$caty['id']] = $caty['jmeno'];
            }
            //print_r($c);
            $tags = implode(",", $c);
            $main_category = "Nezařazeno";
            if(!empty($c)){
                $main_category = $c[array_shift(array_keys($c))];
            }
            echo "<TAGS>" . Html::encode($tags) . "</TAGS>";
            /*if (array_key_exists(19, $c))
                $main_category = 'Zboží';
            if (array_key_exists(450, $c))
                $main_category = 'Jídlo';
            if (array_key_exists(351, $c))
                $main_category = 'Pobyty';
            if (array_key_exists(622, $c))
                $main_category = 'Krása';
            if (array_key_exists(1, $c))
                $main_category = 'Služby';
            if (array_key_exists(552, $c))
                $main_category = 'Zážitky';
            if (array_key_exists(35, $c))
                $main_category = 'Děti';
            if (array_key_exists(256, $c))
                $main_category = 'Móda';
            if (array_key_exists(135, $c))
                $main_category = 'Móda';
            if (array_key_exists(173, $c))
                $main_category = 'Móda';
            if (array_key_exists(200, $c))
                $main_category = 'Móda';
            if (array_key_exists(573, $c))
                $main_category = 'Kultura';
            if (array_key_exists(573, $c))
                $main_category = 'Kultura';
            if (array_key_exists(574, $c))
                $main_category = 'Kultura';
            if (array_key_exists(575, $c))
                $main_category = 'Kultura';
            if (array_key_exists(576, $c))
                $main_category = 'Kultura';
            if (array_key_exists(584, $c))
                $main_category = 'Kultura';
            if (array_key_exists(16, $c))
                $main_category = 'Kurzy';
            if (array_key_exists(588, $c))
                $main_category = 'Sport a Fitness';
            if (array_key_exists(96, $c))
                $main_category = 'Sport a Fitness';
            if (array_key_exists(409, $c))
                $main_category = 'Zdraví';
            if (array_key_exists(423, $c))
                $main_category = 'Relaxace';
            if (array_key_exists(321, $c))
                $main_category = 'Elektro';
            if (array_key_exists(171, $c))
                $main_category = 'Knihy';*/


            echo "<CATEGORY>" . Html::encode($main_category) . "</CATEGORY>";
        }
        echo "<PRIORITY>" . Html::encode($item->Rating) . "</PRIORITY>";
        echo "</DEAL>\n";
    }
}
echo "</MS>\n";
