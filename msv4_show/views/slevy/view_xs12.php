<?php

use yii\helpers\Html;
use app\components\RelatedDeals;
use kartik\widgets\StarRating;
use yii\bootstrap\BootstrapAsset;

$this->registerCssFile("/css/detail.css", [
    'depends' => [BootstrapAsset::className()]]);

$cas = strtotime($model->DEnd);
$seconds = floor($cas - time());

//Days
$days = $seconds / 86400;
$daysf = floor($days);
// extract hours
$hours = ($days - $daysf) * 24;
$hoursf = floor($hours);

// extract minutes
$minutes = floor(($hours - $hoursf) * 60);

$wordsArr = explode(' ', $model->Text);
$numWords = count($wordsArr);
$headingArr = array();
$textArr = array();

if ($numWords >= 10) {
    $i = 0;
    foreach ($wordsArr as $word) {
        if ($i <= 10) {
            $headingArr[$i] = $word;
        } else {
            $textArr[$i] = $word;
        }
        $i++;
    }
} else {
    foreach ($wordsArr as $word) {

        $headingArr[] = $word;
    }
}

$dealHeading = implode(' ', $headingArr);
$dealText = implode(' ', $textArr);

if (count($textArr) >= 1) {
    $dealText = '... ' . $dealText;
}

$dnes = Date("Y-m-d");
$datum = date("Y-m-d", strtotime($model->DStart));
if ($dnes == $datum) {
    $newStyle = 1;
} else {
    $newStyle = 0;
}

$cookie_city_name = \Yii::$app->getRequest()->getCookies()->getValue('mn');
$cookie_city_id = \Yii::$app->getRequest()->getCookies()->getValue('m');

$description = $model->Text;
if (!empty($model->meta->Perex)) {
    $description = $model->meta->Perex;
}

if (!empty($cookie_city_id) && !empty($cookie_city_name)) {

    $this->title = $dealHeading . '.. MéSlevy.cz | ' . $cookie_city_name;
    $city_id = $cookie_city_id;
    $city_name = $cookie_city_name;
} else {

    $this->title = $dealHeading . '.. MéSlevy.cz | ' . $model->cities[0]->Name;
    $city_id = $model->cities[0]->CityId;
    $city_name = $model->cities[0]->Name;
}

if (!empty($city_id) && !empty($city_name)) {
    $this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $city_name . '</span>', 'url' => ['slevy/index', 'm' => $city_id], 'itemprop' => "url"];
}

$keywords_arr = [];
$keywords = $dealHeading;

foreach ($cats as $category_id => $category) {
    $keywords_arr[] = 'sleva ' . $category['jmeno'];
    $this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $category['jmeno'] . '</span>', 'url' => ['slevy/index', 'm' => $city_id, 'k' => $category['id']], 'itemprop' => "url"];
}
if (!empty($keywords_arr))
    $keywords = implode(",", $keywords_arr);

$this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $dealHeading . '</span>', 'url' => ['slevy/view', 'id' => $model->DealId], 'itemprop' => "url"];

$this->registerMetaTag(['name' => 'keywords', 'content' => $keywords]);
$this->registerMetaTag(['name' => 'description', 'content' => Html::encode($description) . ', ' . $model->server->Name], 'description');

$obrazky = [];
if (!empty($model->OriginalImage))
    $obrazky[] = $model->OriginalImage;
if (count($model->images) > 0) {
    foreach ($model->images as $dalsi_obrazek) {
        $obrazky[] = $dalsi_obrazek->UrlLocal;
    }
}
$malych_slajdu = ceil(count($obrazky) / 4);
$pocet_obrazku = count($obrazky);
?>

<div class="row">
    <div class="col-xs-12">
        <div class="row" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <div class="col-md-5 col-md-push-7">
                <div class="deal-detail">
                    <meta itemprop="priceCurrency" content="CZK" />
                    <h1 class="detail-text" itemprop="name"><? echo Html::encode($model->Text); ?></h1>
                    <?
                    if (!empty($model->meta->Perex)) {
                        ?>
                        <div class="detail-perex" itemprop="description">
                            <? echo $model->meta->Perex; ?>
                        </div>
                        <?
                    }
                    ?>
                    <a class="btn btn-green btn-block btn-lg linkout" data-href="<? echo Yii::$app->urlManager->createUrl(['papa/go', "id" => $model->DealId]); ?>" role="button" target="_blank" rel="nofollow">Více na <span itemprop="seller"><? echo $model->server->Name; ?></span></a>
                    <div class="detail-stats">
                        <div class="row">
                            <div class="col-xs-4  br">
                                <div class="detail-stat">
                                    <p class="stat-black"><? echo $model->OPrice; ?> Kč</p>
                                    <p class="stat-grey">Původně</p>
                                </div>
                            </div>
                            <div class="col-xs-4  br">
                                <div class="detail-stat">
                                    <p class="stat-black"><? echo $model->Discount; ?> %</p>
                                    <p class="stat-grey">Sleva</p>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="detail-stat">
                                    <p class="stat-black"><span itemprop="price"><? echo $model->FPrice; ?></span> Kč</p>
                                    <p class="stat-grey">Cena</p>
                                </div>
                            </div>
                        </div>                
                    </div>
                    <div class="customers text-center">
                        <span class="grey">Zveřejněno: </span><span itemprop="validFrom"><? echo date("j.n.Y", strtotime($model->DStart)); ?></span> &nbsp;&nbsp;&nbsp;<span class="grey">Zakoupeno: </span><? echo $model->Customers; ?>x.
                    </div>
                    <div class="timeleft">
                        Platí ještě: 
                    </div>
                    <?
                    $time_color = 'detail-green';
                    if ($days < 0) {
                        $time_color = 'grey';
                    } else {
                        if ($days > 1) {
                            $time_color = 'detail-green';
                        } else {
                            $time_color = 'detail-red';
                        }
                    }
                    ?>
                    <div class="detail-time"><span class="glyphicon glyphicon-time <? echo $time_color; ?>" aria-hidden="true"></span> 
                        <time itemprop="availabilityEnds" datetime="<? echo date("j.n.Y", strtotime($model->DEnd)); ?>">
                            <?php
                            if ($days < 0) {
                                echo ' <s><span class=\"' . $time_color . '\">' . date("j.n.Y", strtotime($model->DEnd)) . '</span></s>';
                            } else {
                                echo " <span class=\"'.$time_color.'\">" . $daysf . " <span class=\"grey\">d.</span> " . $hoursf . " <span class=\"grey\">h.</span> " . $minutes . " <span class=\"grey\">m.</span></span>";
                            }
                            ?>
                        </time>
                        <meta itemprop="availabilityStarts" content="<? echo date("j.n.Y", strtotime($model->DStart)); ?>" />
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                                <meta itemprop="reviewCount" content = "<? echo $model->Customers; ?>">
                                <meta itemprop="worstRating" content = "0">
                                <meta itemprop="ratingValue" content = "<? echo $model->Rating; ?>">
                                <meta itemprop="bestRating" content = "10">
                                <?
                                echo StarRating::widget([
                                    'name' => 'rating_' . $model->DealId,
                                    'value' => $model->Rating,
                                    'pluginOptions' => [
                                        'size' => 'xs transparent-light radius',
                                        'stars' => 3,
                                        'min' => 0,
                                        'max' => 10,
                                        'step' => 0.1,
                                        'readonly' => true,
                                        'showClear' => false,
                                        'showCaption' => false,
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-social">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=http://www.meslevy.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-facebook"><i class="fa fa-facebook"></i></a>
                                <a href="https://twitter.com/home?status=Bezva%20sleva%20:)%0Ahttp://www.meslevy.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-twitter"><i class="fa fa-twitter"></i></a>
                                <a href="https://plus.google.com/share?url=http://www.meslevy.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-google-plus"><i class="fa fa-google-plus"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget">
                    <div class="widget-title">
                        <h2>Prodejce</h2>
                    </div> 
                    <div class="text-center">
                        <a class="linkout" data-href="<? echo Yii::$app->urlManager->createUrl(['papa/go', "id" => $model->DealId]); ?>" target="_blank" rel="nofollow">
                            <img src="http://images.meslevy.cz/loga/<? echo $model->server->Logo; ?>" class="detail-logo" alt="<? echo $model->server->Name; ?>" />
                        </a>
                    </div>
                </div>
                <? if (!Yii::$app->user->isGuest) { ?>
                    <div class="widget">
                        <div class="widget-title">
                            <h2>Admin info</h2>
                        </div> 
                        <div class="text-center">
                            <?
                            if (!empty($model->FeedKws)) {
                                echo 'Od prodejce: '.$model->FeedKws;
                            }
                            ?>
                        </div>
                    </div>
                <? } ?>
            </div><!-- /col-md-5 -->
            <div class="col-md-7 col-md-pull-5">
                <? if ($pocet_obrazku > 0) { ?>
                    <div id="carousel" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <? foreach ($obrazky as $klic_obrazku => $adresa_obrazku) { ?>
                                <div class="item<? if ($klic_obrazku == 0) echo ' active'; ?>">
                                    <div class="detail-img" style="background:url(<? echo $adresa_obrazku; ?>) center center no-repeat;background-size: cover;"></div>
                                    <meta itemprop="image" content="<? echo $adresa_obrazku; ?>" />
                                </div>
                            <? } ?>
                        </div>
                    </div> 
                    <div class="clearfix">
                        <div id="thumbcarousel" class="carousel slide" data-interval="false">
                            <div class="carousel-inner">
                                <?
                                $op = 0;
                                for ($o = 0; $o < $pocet_obrazku; $o++) {
                                    $op++;
                                    if ($op == 1) {
                                        if ($o == 0) {
                                            echo '<div class="item active">';
                                        } else {
                                            echo '<div class="item">';
                                        }
                                    }
                                    ?>
                                    <div data-target="#carousel" data-slide-to="<? echo $o; ?>" class="thumb detail-c-img" style="background:url(<? echo $obrazky[$o]; ?>) center center no-repeat;background-size: cover;"></div>
                                    <?
                                    if ($op == 4 || $o == $pocet_obrazku - 1) {
                                        echo '</div>';
                                        $op = 0;
                                    }
                                }
                                ?>
                            </div><!-- /carousel-inner -->
                            <? if ($malych_slajdu > 1) { ?>
                                <a class="left carousel-control" href="#thumbcarousel" role="button" data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </a>
                                <a class="right carousel-control" href="#thumbcarousel" role="button" data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            <? } ?>
                        </div> <!-- /thumbcarousel -->
                    </div><!-- /clearfix -->
                <? } ?>
                <div class="deal-description">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active"><a href="#podobne" data-toggle="tab">Podobné Slevy</a></li>
                        <li role="presentation" <? if (empty($model->meta->DescriptionFull) || (int) $model->server->DisplayDescription == 0) echo ' class="disabled" title="Popis není k dispozici"'; ?>><a href="#popis" data-toggle="tab">Popis Slevy</a></li>
                    </ul>
                    <div class="tab-content">
                        <?
                        if (!empty($model->meta->DescriptionFull) && (int) $model->server->DisplayDescription == 1) {
                            ?>    
                            <div id="popis" class="tab-pane">
                                <? echo $model->meta->DescriptionFull; ?>
                            </div>
                            <?
                        }
                        ?>
                        <div id="podobne" class="tab-pane active">
                            <? echo RelatedDeals::widget(['id' => $model->DealId, 'cid' => $city_id, 'cnm' => $city_name, 'tags' => $cats]); ?>
                        </div>
                    </div>
                </div>
            </div> <!-- /col-md-7 -->
        </div>
    </div>
</div>

