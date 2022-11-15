<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SlevySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="slevy-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'DealId') ?>

    <?= $form->field($model, 'Hash') ?>

    <?= $form->field($model, 'OwnerId') ?>

    <?= $form->field($model, 'ServerId') ?>

    <?= $form->field($model, 'CategoryId') ?>

    <?php // echo $form->field($model, 'Text') ?>

    <?php // echo $form->field($model, 'TextFull') ?>

    <?php // echo $form->field($model, 'SearchText') ?>

    <?php // echo $form->field($model, 'FeedKws') ?>

    <?php // echo $form->field($model, 'FPrice') ?>

    <?php // echo $form->field($model, 'OPrice') ?>

    <?php // echo $form->field($model, 'Discount') ?>

    <?php // echo $form->field($model, 'DStart') ?>

    <?php // echo $form->field($model, 'DEnd') ?>

    <?php // echo $form->field($model, 'Url') ?>

    <?php // echo $form->field($model, 'Image') ?>

    <?php // echo $form->field($model, 'OriginalImage') ?>

    <?php // echo $form->field($model, 'Customers') ?>

    <?php // echo $form->field($model, 'Status') ?>

    <?php // echo $form->field($model, 'Rating') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
