<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'MéSlevy.cz | Kontakt';
$this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">Kontakt</span>', 'url' => ['site/contact'], 'itemprop' => "url"];
?>

<div class="widget contact">
    <div class="widget-title">
        <h2>Kontaktní Formulář</h2>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="site-contact">

                <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

                    <div class="alert alert-success">
                        Děkujeme za Vaši zprávu. Budeme se jí věnovat hned, jakmile to bude možné.
                    </div>

                <?php else: ?>

                    <p>
                        Máte nějaké dotazy, nebo by jste chtěli spolupracovat? Rádi o Vás uslyšíme.
                    </p>


                    <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                    <?= $form->field($model, 'name') ?>
                    <?= $form->field($model, 'email') ?>
                    <?= $form->field($model, 'subject') ?>
                    <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
                    <?=
                    $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                    ])
                    ?>
                    <div class="form-group text-center">
                        <?= Html::submitButton('Odeslat', ['class' => 'btn btn-success btn-lg btn-block', 'name' => 'contact-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>

                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <h4>Často kladené dotazy</h4>

            <p>Některé dotazy se opakují častěji než jiné a abychom Vám ušetřili čas, vypsali jsme je níže. Věříme, že Vám tyto informace pomohou!</p>
            <div class="well">

                <h4 class="green">Mám problém s nákupem</h4>

                <p>Nákup na každém slevovém serveru funguje jinak a každý slevový server nabízí jiné platební metody. V případě problémů s nákupem kontaktujte provozovatele slevového serveru.</p>

                <h4 class="green">Neobdržel jsem zakoupený slevový voucher</h4>

                <p>V případě, že jste neobdrželi zakoupený slevový voucher nebo máte jiný problém s konkrétním nákupem, kontaktujte provozovatele slevového serveru, kde jste nákup provedli. MéSlevy.cz poskytují pouze přehled slev konkrétních slevových serverů. Nejsme prodejci ani poskytovatelé slev.</p>

                <h4 class="green">Poskytuji službu, chci u Vás uveřejnit svoji nabídku</h4>

                <p>MéSlevy.cz zobrazují nabídky pouze od slevových serverů. Pokud chcete uveřejnit nabídku slevy ve své provozovně, oslovte některý ze slevových serverů.</p>
            </div>
            <h4>Provozovatel</h4>
            <ul class="list-unstyled italic well">
                <li>Marian Šimčík - ArienSoft</li>
                <li>IČO: 87322102</li>
                <li>Právní forma: Fyzická osoba podnikající dle živnostenského zákona nezapsaná v obchodním rejstříku</li>
                <li>Adresa: Kraiczova 1040, 757 01 Valašské Meziříčí</li>
                <li>Datum vzniku: 20. září 2010 </li>
                <ul>
                    </div>
                    </div>
                    </div>