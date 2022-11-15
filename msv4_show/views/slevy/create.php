<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Slevy */

$this->title = 'Create Slevy';
$this->params['breadcrumbs'][] = ['label' => 'Slevies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="slevy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
