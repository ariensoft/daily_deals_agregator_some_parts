<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Kategorie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kategorie-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rodic')->textInput() ?>

    <?= $form->field($model, 'jmeno')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'kw')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'feed_kw')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'obrazek')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'priorita')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
