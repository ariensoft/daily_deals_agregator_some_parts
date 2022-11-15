<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SlevySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$city_id = Yii::$app->params['city_id'];
$city_name = Yii::$app->params['city_name'];
$title = '';
$title_parts = array('MéSlevy.cz');
$title_parts[] = 'Slevy ' . $city_name;
$group_desc_part = '';
$city_tags_part = ' ' . $city_name;
$city_desc_part = 've městě ' . $city_name;
$item_class = 'item col-md-6';
$summary_text = '<h3>' . $city_name . ' Dnes {summary} slev</h3>';

$this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $city_name . '</span>', 'url' => ['slevy/index', 'm' => $city_id], 'itemprop' => "url"];

if (Yii::$app->controller->action->id != 'index') {
    $this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . ucwords(Yii::$app->controller->action->id) . '</span>', 'url' => ['slevy/' . Yii::$app->controller->action->id, 'm' => $city_id], 'itemprop' => "url"];
    $title_parts[] = ucwords(Yii::$app->controller->action->id);
    $item_class = 'item col-md-6 col-lg-4 col-sm-12';
}

if (!empty($parrent_info)) {
    foreach ($parrent_info as $parrent_cat) {
        $this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $parrent_cat['name'] . '</span>', 'url' => ['slevy/index', 'm' => $city_id, 'k' => $parrent_cat['id']], 'itemprop' => "url"];
        $title_parts[] = $parrent_cat['name'];
    }
    end($parrent_info);
    $key = key($parrent_info);
    $group_desc_part = 'v kategorii ' . $parrent_info[$key]['name'] . ' ';
    $summary_text = '<h3>' .$parrent_info[$key]['name'] . ' ' . $city_name . ' Dnes {summary} slev</h3>';
}

if (!empty($_GET['s'])) {
    $title_parts[] = 'Hledat: ' . $_GET['s'];
    $this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">Hledat: ' . $_GET['s'] . '</span>', 'url' => ['slevy/index', 'm' => $city_id, 's' => $_GET['s']], 'itemprop' => "url"];
    $this->registerMetaTag(['name' => 'keywords', 'content' => 'hledat slevy' . $city_tags_part . ',hledat sleva' . $city_tags_part . ',hledat hromadné slevy' . $city_tags_part . ',hledat slevové akce' . $city_tags_part . '']);
    $this->registerMetaTag(['name' => 'description', 'content' => 'Najděte si slevy ' . $city_desc_part . ' co opravdu frčí. Nakupujte slevové vouchery na ' . $_GET['s'] . ' chytře a výhodně s MéSlevy.cz!'], 'description');
} else {
    $this->registerMetaTag(['name' => 'keywords', 'content' => 'slevy' . $city_tags_part . ',sleva' . $city_tags_part . ',hromadné slevy' . $city_tags_part . ',slevové akce' . $city_tags_part . '']);
}

if (!empty($_GET['page'])) {
    $title_parts[] = 'Stránka: ' . $_GET['page'];
}

$sort_btn_class = 'btn-default';
if (!empty($_GET['sort'])) {
    $sort_btn_class = 'btn-default';
}
$sorter_text = 'Seřadit slevy';

if (!empty($_GET['sort'])) {
    $sort = '';
    switch ($_GET['sort']) {
        case 'Discount':
            $sort = 'od nejmenší slevy';
            $sorter_text = 'Nejmenší sleva';
            break;
        case '-Discount':
            $sort = 'od největší slevy';
            $sorter_text = 'Největší sleva';
            break;
        case 'FPrice':
            $sort = 'nejlevnější';
            $sorter_text = 'Nejlevnější';
            break;
        case '-FPrice':
            $sort = 'nejdražší';
            $sorter_text = 'Nejdražší';
            break;
        case 'DStart':
            $sort = 'nejstarší';
            $sorter_text = 'Nejstarší slevy';
            break;
        case '-DStart':
            $sort = 'nejnovější';
            $sorter_text = 'Nejnovější slevy';
            break;
        case 'DEnd':
            $sort = 'končí dnes';
            $sorter_text = 'Končí dnes';
            break;
        case '-DEnd':
            $sort = 'končí za dlouho';
            $sorter_text = 'Hodně Času';
            break;
        case 'Rating':
            $sort = 'nepopulární';
            $sorter_text = 'Nepopulární';
            break;
        case '-Rating':
            $sort = 'populární';
            $sorter_text = 'Populární';
            break;
    }

    $title_parts[] = 'Seřadit: ' . $sort;
}

if (!empty($title_parts)) {
    $title = $title . implode(" | ", $title_parts);
}

$this->title = $title;

switch (Yii::$app->controller->action->id) {
    case 'index':
        $summary_text = $summary_text;
        $this->registerMetaTag(['name' => 'description', 'content' => 'Jen slevy co opravdu frčí ' . $city_desc_part . '! Co je dnes na MéSlevy.cz ' . $group_desc_part . 'nového? '. implode(" | ", $title_parts)], 'description');
        break;
    case 'novinky':
        $summary_text = '<h3>' . $city_name . ' Dnes nových {summary} slev</h3>';
        $this->registerMetaTag(['name' => 'description', 'content' => 'Každý den nové slevy ' . $city_desc_part . '. Které si dnes vysloužily zveřejnění na MéSlevy.cz? '. implode(" | ", $title_parts)], 'description');
        break;
    case 'top24':
        $summary_text = '<h3>' . $city_name . ' Hitpáráda nej {summary} slev</h3>';
        $this->registerMetaTag(['name' => 'description', 'content' => 'Hitpáráda nejlepších slev ' . $city_desc_part . '. Jaká sleva je dnes na MéSlevy.cz první?'], 'description');
        break;
    case 'slevogedon':
        $summary_text = '<h3>' . $city_name . ' Dnes končí {summary} slev</h3>';
        $this->registerMetaTag(['name' => 'description', 'content' => 'Dnes končí na MéSlevy.cz hromada slev ' . $city_desc_part . '. Ať Vám žádná neuteče! '. implode(" | ", $title_parts)], 'description');
        break;
}
?>

<div class="slevy-index row">

    <?php /* echo $this->render('_search', ['model' => $searchModel]); */ ?>

    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => $item_class],
        'itemView' => '_view',
        'layout' => '<div class="col-lg-12">'
        . '<div class="deals-top"><div class="row">'
        . '<div class="col-md-6">' . $summary_text . '</div>'
        . '<div class="col-md-6 text-right">'
        . '                                       <div class="btn-group">     
                                                <button class="btn btn-xs sort-btn ' . $sort_btn_class . ' dropdown-toggle" type="button" id="SortMenu" data-toggle="dropdown" aria-expanded="false">
                                                ' . $sorter_text . '
                                                <span class="glyphicon glyphicon-menu-down"></span>
                                                </button>
                                                    {sorter}
                                            </div>'
        . '</div></div>'
        . '</div>'
        . '</div>'
        . '{items}'
        . '                        <div class="text-center col-lg-12">
                            {pager}
                        </div>',
        'summary' => '{totalCount}',
        'emptyText' => $this->render( '_list_empty', ['parrent_info'=> $parrent_info] ),
        'emptyTextOptions' => ['class' => 'widget'],
        'sorter' => [
            'options' => ['class' => 'dropdown-menu', 'role' => 'menu', 'aria-labelledby' => 'SortMenu'],
        ],
        'pager' => [
            //'options' => ['class' => 'pagination col-lg-12 text-center'],
            'firstPageLabel' => '<span class="glyphicon glyphicon-fast-backward"></span>',
            'lastPageLabel' => '<span class="glyphicon glyphicon-fast-forward"></span>',
            'nextPageLabel' => '<span class="glyphicon glyphicon-step-forward"></span>',
            'prevPageLabel' => '<span class="glyphicon glyphicon-step-backward"></span>',
            'hideOnSinglePage' => true,
        ],
    ])
    ?>

</div>

