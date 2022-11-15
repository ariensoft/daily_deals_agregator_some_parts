<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\components\DnavbarWidget;
use kartik\widgets\Typeahead;
use app\components\NavMegamenu;
use app\components\CatList;
use app\components\MainSearchForm;
use app\models\Visitors;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
//Visitors::startSpy();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

        <?= Html::csrfMetaTags() ?>

        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- Start of Smartsupp Live Chat script -->
        <!-- Smartsupp Live Chat script -->
        <script type="text/javascript">
            var _smartsupp = _smartsupp || {};
            _smartsupp.key = '4172e4b14eb7c6d62dd49e60cffea8949cb7d758';
            window.smartsupp || (function (d) {
                var s, c, o = smartsupp = function () {
                    o._.push(arguments)
                };
                o._ = [];
                s = d.getElementsByTagName('script')[0];
                c = d.createElement('script');
                c.type = 'text/javascript';
                c.charset = 'utf-8';
                c.async = true;
                c.src = '//www.smartsuppchat.com/loader.js?';
                s.parentNode.insertBefore(c, s);
            })(document);
        </script>
    </head>

    <body>
        <?php $this->beginBody() ?>

        <?php
        NavBar::begin([
            'brandLabel' => '<img alt="Meslevy.cz" src="http://images.meslevy.cz/meslevy.png" id="logo" />',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-static-top navbar-default navbarx',
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'nav navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Vše', 'url' => ['/slevy/index', 'm' => Yii::$app->params['city_id']]],
                ['label' => 'Novinky', 'url' => ['/slevy/novinky', 'm' => Yii::$app->params['city_id']]],
                ['label' => 'Top24', 'url' => ['/slevy/top24', 'm' => Yii::$app->params['city_id']]],
                ['label' => 'Slevogedon', 'url' => ['/slevy/slevogedon', 'm' => Yii::$app->params['city_id']]],
            //['label' => 'O nás', 'url' => ['/site/about']],
            //['label' => 'Kontakt', 'url' => ['/site/contact']],
            /* Yii::$app->user->isGuest ?
              ['label' => 'Login', 'url' => ['/site/login']] :
              ['label' => 'Logout (' . Yii::$app->user->identity->username . ')',
              'url' => ['/site/logout'],
              'linkOptions' => ['data-method' => 'post']], */
            ],
        ]);
        echo DnavbarWidget::widget();
        ?>

        <?
        NavBar::end();
        ?>
        <div class="menu-container">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <? //echo NavMegamenu::widget(); ?>
                        <? echo MainSearchForm::widget(); ?>
                    </div>
                </div>
            </div>
        </div>
        <? if (isset($this->params['breadcrumbs'])) { ?>
            <div class="breads-container">    
                <div class="container">

                    <?=
                    Breadcrumbs::widget([
                        'homeLink' => ['label' => '<span itemprop="title">MéSlevy.cz</span>', 'url' => Yii::$app->homeUrl, 'itemprop' => "url"],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        'options' => ['class' => 'breads'],
                        //'tag' => 'div',
                        'itemTemplate' => "<li itemscope itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"bread\">{link}</li>\n",
                        'encodeLabels' => false,
                    ])
                    ?>
                </div>
            </div>
        <? } else { ?>
            <div class="breads-container-empty"></div>
        <? } ?>

        <div class="container">

            <div class="row">

                <div class="col-xs-12" id="content">
                    <?= $content ?>
                </div><!--/.col-xs-12.col-sm-9-->

            </div><!--/row-->

        </div><!--/.container-->
        <div class="footer">
            <div class="below">
                <a class="btt-btn img-circle" href="#"><span class="glyphicon glyphicon-arrow-up"></span><br> NAHORU</a>
                <div class="container">
                    <div class="row">
                        <div class="col-md-5 col-xs-12">
                            <div class="below-widget">
                                <div class="blockquote">
                                    Ty nejlepší slevy z českých slevových portálů na jednom místě. To jsou MéSlevy.cz. Najdi zde a kup u prodejce.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-xs-12">

                        </div>
                        <div class="col-md-2 col-xs-12">
                            <div class="below-widget">
                                <span class="small">&copy; MéSlevy 2015</span><br>
                                <a href="<? echo Yii::$app->urlManager->createUrl(['site/contact']); ?>">Kontakt</a>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-12">
                            <div class="below-widget">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=http://www.meslevy.cz/" target="_blank" rel="nofollow" class="btn"><i class="fa fa-facebook"></i></a>
                                <a href="https://twitter.com/home?status=Jen%20dobré%20slevy%0Ahttp://www.meslevy.cz/" target="_blank" rel="nofollow" class="btn"><i class="fa fa-twitter"></i></a>
                                <a href="https://plus.google.com/share?url=http://www.meslevy.cz/" target="_blank" rel="nofollow" class="btn"><i class="fa fa-google-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <? if (isset($this->params['breadcrumbs'])) { ?>
                <div class="breads-container-bottom">
                    <div class="container">
                        <?=
                        Breadcrumbs::widget([
                            'homeLink' => ['label' => '<span itemprop="title">MéSlevy.cz</span>', 'url' => Yii::$app->homeUrl, 'itemprop' => "url"],
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                            'options' => ['class' => 'breads'],
                            //'tag' => 'div',
                            'itemTemplate' => "<li itemscope itemtype=\"http://data-vocabulary.org/Breadcrumb\" class=\"bread\">{link}</li>\n",
                            'encodeLabels' => false,
                        ])
                        ?>
                    </div>
                </div>
            <? } ?>
        </div>
        <footer>

        </footer>
        <? if (Yii::$app->user->isGuest) { ?>
            <script>
                (function (i, s, o, g, r, a, m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function () {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                            m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a, m)
                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                ga('create', 'UA-19461556-1', 'auto');
                ga('send', 'pageview');

            </script>
        <? } ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>