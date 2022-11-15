<?php

use kartik\widgets\StarRating;
use yii\bootstrap\BootstrapAsset;

$this->registerCssFile("/css/related_deals.css", [
    'depends' => [BootstrapAsset::className()]]);

if (count($data) > 0) {
    ?>
    <div class="row">

        <?
        foreach ($data as $deal) {

            $cas = strtotime($deal['DEnd']);
            $seconds = floor($cas - time());
            $days = $seconds / 86400;
            $daysf = floor($days);
            $hours = ($days - $daysf) * 24;
            $hoursf = floor($hours);
            $minutes = floor(($hours - $hoursf) * 60);

            $wordsArr = explode(' ', $deal['Text']);
            $numWords = count($wordsArr);
            $newArr = array();

            if ($numWords >= 8) {
                $i = 0;
                foreach ($wordsArr as $word) {
                    if ($i <= 8) {
                        $newArr[$i] = $word;
                        $i++;
                    }
                }
                $newArr[9] = ' ...';
                $dealText = implode(' ', $newArr);
            } else {
                foreach ($wordsArr as $word) {

                    $newArr[] = $word;
                }
                $dealText = implode(' ', $newArr);
            }

            $dnes = Date("Y-m-d");
            $datum = date("Y-m-d", strtotime($deal['DStart']));
            if ($dnes == $datum) {
                $newStyle = 1;
            } else {
                $newStyle = 0;
            }
            ?>

            <div class="col-sm-12 col-md-6">
                <div class="related-deal">
                    <a href="<? echo Yii::$app->urlManager->createUrl(['slevy/view', "id" => $deal['DealId']]); ?>" title="<? echo $deal['Text']; ?>">
                        <div class="related-deal-img" style="background:url(<? echo $deal['Image']; ?>) center center no-repeat;background-size: cover;">
                            <div class="related-deal-discount">-<? echo $deal['Discount']; ?>% </div>
                            <div class="related-deal-text">
                                <? echo $dealText; ?>
                            </div>
                        </div>
                    </a>
                    <div class="related-footer">
                        <div class="row">
                            <div class="col-xs-6 br">
                                <div class="related-deal-stat">
                                    <span class="related-price"><? echo $deal['FPrice']; ?></span> <span class="grey">Kč</span>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="related-deal-stat">
                                    <img src="http://images.meslevy.cz/loga/<? echo $deal['server']['Logo']; ?>" class="related-deal-logo" alt="<? echo $deal['server']['Name']; ?>" />
                                </div>
                            </div>
                        </div>                
                    </div>
                </div>
            </div>

        <? } ?>

    </div>
    <?
    if ($diff > 0) {
        $rev_cities = array_reverse($cities);
        ?>
        <a class="btn btn-green btn-block btn-lg" href="<? echo Yii::$app->urlManager->createUrl(['slevy/index', "m" => $rev_cities[0], "k" => $found]); ?>" role="button" >Všechny podobné slevy</a>
    <? } ?>

<? } ?>
    
