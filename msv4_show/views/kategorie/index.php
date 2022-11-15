<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Kategories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kategorie-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Kategorie', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'rodic',
            'jmeno',
            'kw',
            'feed_kw',
            // 'url:url',
            // 'obrazek',
            // 'priorita',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
