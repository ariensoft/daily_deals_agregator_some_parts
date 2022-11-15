<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Servers */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servers-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ServerId',
            'Payments',
            'Name',
            'FeedUrl:url',
            'FeedType',
            'Priority',
            'UrlAdd:url',
            'AddType',
            'Element',
            'Insurance',
        ],
    ]) ?>

</div>
