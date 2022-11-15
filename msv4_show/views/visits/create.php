<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Visits */

$this->title = 'Create Visits';
$this->params['breadcrumbs'][] = ['label' => 'Visits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visits-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
