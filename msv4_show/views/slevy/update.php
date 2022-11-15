<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Slevy */

$this->title = 'Update Slevy: ' . ' ' . $model->DealId;
$this->params['breadcrumbs'][] = ['label' => 'Slevies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->DealId, 'url' => ['view', 'id' => $model->DealId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="slevy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
