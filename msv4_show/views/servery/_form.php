<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Servers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Payments')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FeedUrl')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FeedType')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Priority')->textInput() ?>

    <?= $form->field($model, 'UrlAdd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'AddType')->textInput() ?>

    <?= $form->field($model, 'Element')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Insurance')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
