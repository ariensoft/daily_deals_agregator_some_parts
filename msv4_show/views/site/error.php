<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>

<div class="widget">
    <div class="site-error">
        <div class="widget-title">
            <h2><?= Html::encode($this->title) ?></h2>
        </div>
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>

        <p>
            Nastala výše zmíněná chyba.
        </p>
        <p>
            Prosím kontaktujte nás, pokud se domníváte, že jde o systémovou chybu. Děkujeme.
        </p>

    </div>
</div>