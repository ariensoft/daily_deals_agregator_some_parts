<?php

use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use yii\helpers\ArrayHelper;

?>

<form class="main-search-form" role="search" action="<? echo(Yii::$app->urlManager->createUrl(['slevy/index'])); ?>" method="get">

    <div class="row">
        <div class="col-md-3 col-sm-12">
            <?
            $search_placeholder = 'Hledaný text';
            if (!empty($_GET['s'])) {
                $search_placeholder = $_GET['s'];
            }
            echo Typeahead::widget([
                'id' => 's',
                'name' => 's',
                'options' => [
                    'placeholder' => $search_placeholder
                ],
                'scrollable' => false,
                'pluginOptions' => ['highlight' => true, 'allowClear' => true],
                'dataset' => [
                    [
                        'prefetch' => Yii::$app->urlManager->createUrl(['kategorie/tag-list']),
                        'limit' => 10
                    ]
                ]
            ]);
            ?>
        </div>
        <div class="col-md-3 col-sm-12">
            <?
            echo Select2::widget([
                'name' => 'k',
                'value' => isset($_GET['k']) ? $_GET['k'] : $category,
                'data' => ArrayHelper::map($categories, 'id', 'jmeno'),
                'theme' => Select2::THEME_BOOTSTRAP,
                'size' => Select2::MEDIUM,
                'options' => ['placeholder' => 'Kategorie ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-3 col-sm-12">
            <?
            echo Select2::widget([
                'name' => 'm',
                'value' => isset($_GET['m']) ? $_GET['m'] : '',
                'data' => ArrayHelper::map($cities, 'id', 'name'),
                'theme' => Select2::THEME_BOOTSTRAP,
                'size' => Select2::MEDIUM,
                'options' => ['placeholder' => 'Vaše město ...'],
                'pluginOptions' => [
                    'allowClear' => false
                ],
            ]);
            ?>
        </div>
        <div class="col-md-3 col-sm-12">
            <button type="submit" class="btn btn-green btn-block" data-toggle="offcanvas">Hledat</button>
        </div>
    </div>

</form>


