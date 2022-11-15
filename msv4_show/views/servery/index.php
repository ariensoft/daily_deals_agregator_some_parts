<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\BootstrapAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->registerCssFile("/css/servery.css", [
    'depends' => [BootstrapAsset::className()]]);

$this->title = 'Servery';
$this->params['breadcrumbs'][] = $this->title;
$item_class = 'item col-md-6';
?>
<div class="servers-index row">

    <h1><?= Html::encode($this->title) ?></h1>
    <?
    echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_view',
    'itemOptions' => ['class' => $item_class],
    ]);
    ?>

</div>
