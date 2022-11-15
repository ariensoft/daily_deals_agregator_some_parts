<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Servers */

$this->title = 'Update Servers: ' . ' ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->ServerId]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="servers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
