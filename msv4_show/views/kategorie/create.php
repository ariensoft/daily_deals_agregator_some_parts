<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Kategorie */

$this->title = 'Create Kategorie';
$this->params['breadcrumbs'][] = ['label' => 'Kategories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kategorie-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
