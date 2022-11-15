<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Visitors */

$this->title = 'Create Visitors';
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitors-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
