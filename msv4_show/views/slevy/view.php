<?php

use yii\helpers\Html;
//use app\components\RelatedDeals;
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

if ($numWords >= 4) {
    $i = 0;
    foreach ($wordsArr as $word) {
        if ($i <= 4) {
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
$keywords = '';
foreach ($model->categories as $category) {
    $keywords_arr[] = 'slevy ' . $category->jmeno;
    $this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $category->jmeno . '</span>', 'url' => ['slevy/index', 'm' => $city_id, 'k' => $category->kategorie_id], 'itemprop' => "url"];
}
$keywords = implode(",", $keywords_arr);

$this->params['breadcrumbs'][] = ['label' => '<span itemprop="title">' . $dealHeading . '</span>', 'url' => ['slevy/view', 'id' => $model->DealId], 'itemprop' => "url"];

$this->registerMetaTag(['name' => 'keywords', 'content' => $keywords]);
$this->registerMetaTag(['name' => 'description', 'content' => Html::encode($model->Text) . ', ' . $model->server->Name], 'description');
?>

<div class="row">
    <div class="col-xs-12">
        <div class="row deal-detail">
            <div class="col-md-6 detail-left">
                <div class="deal-discount">-<? echo $model->Discount; ?>% </div>
                <div class="detail-img" style="background:url(<? echo $model->OriginalImage; ?>) center center no-repeat;background-size: cover;">
                    <div class="product-social">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=http://www.setrilci.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-facebook"><i class="fa fa-facebook"></i></a>
                        <a href="https://twitter.com/home?status=Bezva%20sleva%20:)%0Ahttp://www.setrilci.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-twitter"><i class="fa fa-twitter"></i></a>
                        <a href="https://plus.google.com/share?url=http://www.setrilci.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-google-plus"><i class="fa fa-google-plus"></i></a>
                    </div>
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
            </div>
            <div class="col-md-6 detail-right">
                <div class="detail-stats">
                    <div class="detail-text"><? echo Html::encode($model->Text); ?></div>
                    <ul itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="list-inline detail-info">
                        <meta itemprop="priceCurrency" content="CZK" />
                        <li><span class="deal-price" itemprop="price"><? echo $model->FPrice; ?></span> Kč</li>        
                        <li><span class="deal-oprice"><? echo $model->OPrice; ?> Kč </span></li>
                        <li><span class="glyphicon glyphicon-user grey" aria-hidden="true"></span> <? echo $model->Customers; ?> x</li>
                    </ul>
                </div>
                <div class="detail-footer">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="detail-time"><span class="glyphicon glyphicon-time grey" aria-hidden="true"></span> 
                                <time itemprop="priceValidUntil" datetime="<? echo date("Y-m-d", strtotime($model->DEnd)); ?>">
                                    <?php
                                    if ($days < 0) {
                                        echo ' <s><span class=\"red\">' . date("j.n.Y", strtotime($model->DEnd)) . '</span></s>';
                                    } else {
                                        if ($days > 1) {
                                            echo " <span class=\"blue timeleft\">" . $daysf . " <span class=\"grey\">d.</span> " . $hoursf . " <span class=\"grey\">h.</span> " . $minutes . " <span class=\"grey\">m.</span></span>";
                                        } else {
                                            echo " <span class=\"red timeleft\">" . $daysf . " <span class=\"grey\">d.</span> " . $hoursf . " <span class=\"grey\">h.</span> " . $minutes . " <span class=\"grey\">m.</span></span>";
                                        }
                                    }
                                    ?>
                                </time>
                            </div>
                        </div>
                        <div class="col-sm-7 text-right">
                            <a class="btn btn-default" href="<? echo Yii::$app->urlManager->createUrl(['papa/go', "id" => $model->DealId]); ?>" role="button" target="_blank">Zobrazit na <? echo $model->server->Name; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <? if (count($model->images) > 0) { ?> 

        <div class="col-xs-12">
            <div class="row">
                <div class="col-md-12">
                    <div id="Carousel" class="carousel slide">
                        <?
                        $num_slides = ceil(count($model->images) / 4);

                        if ($num_slides > 1) {
                            ?>
                            <ol class="carousel-indicators">
                                <? for ($i = 0; $i < $num_slides; $i++) { ?>
                                    <li data-target="#Carousel" data-slide-to="<? echo $i; ?>" <? if ($i == 0) echo 'class="active"'; ?>></li> 
                                <? } ?>
                            </ol>
                        <? } ?>
                        <!-- Carousel items -->
                        <div class="carousel-inner">
                            <?
                            $ic = 0;
                            $igc = 0;
                            $num_images = count($model->images);
                            foreach ($model->images as $next_image) {
                                $ic ++;
                                $igc ++;

                                if ($ic == 1) {
                                    if ($igc < 4) {
                                        echo '<div class="item active">
                                    <div class="row">';
                                    } else {
                                        echo '<div class="item">
                                    <div class="row">';
                                    }
                                }
                                ?>

                                <div class="col-md-3">
                                    <div class="thumbnail detail-c-img" style="background:url(<? echo $next_image->UrlLocal; ?>) center center no-repeat;background-size: cover;"></div>
                                </div>
                                <?
                                if ($ic == 4 || $igc == $num_images) {
                                    echo "</div><!--.row--> \n
                                            </div><!--.item--> \n";
                                    $ic = 0;
                                }
                                ?>
                            <? } ?>
                        </div><!--.carousel-inner-->
                        <? if ($num_slides > 1) { ?>
                            <a data-slide="prev" href="#Carousel" class="left carousel-control">‹</a>
                            <a data-slide="next" href="#Carousel" class="right carousel-control">›</a>
                        <? } ?>
                    </div><!--.Carousel-->

                </div>
            </div>
        </div>
    <? } ?>
</div>