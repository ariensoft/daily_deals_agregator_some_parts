<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Slevy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="slevy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'OwnerId')->textInput() ?>

    <?= $form->field($model, 'ServerId')->textInput() ?>

    <?= $form->field($model, 'CategoryId')->textInput() ?>

    <?= $form->field($model, 'Text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TextFull')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'SearchText')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FeedKws')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FPrice')->textInput() ?>

    <?= $form->field($model, 'OPrice')->textInput() ?>

    <?= $form->field($model, 'Discount')->textInput() ?>

    <?= $form->field($model, 'DStart')->textInput() ?>

    <?= $form->field($model, 'DEnd')->textInput() ?>

    <?= $form->field($model, 'Url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'OriginalImage')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Customers')->textInput() ?>

    <?= $form->field($model, 'Status')->textInput() ?>

    <?= $form->field($model, 'Rating')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
