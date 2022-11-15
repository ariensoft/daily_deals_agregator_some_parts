<?

use yii\helpers\Html;

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

$dealText = $model->Text;

if (mb_strlen($dealText, 'UTF-8') > 70) {

    $wordsArr = explode(' ', $model->Text);
    $i = 0;
    $text_len = 0;
    foreach ($wordsArr as $word) {
        $word_len = mb_strlen($word, 'UTF-8');
        if ($text_len <= 70) {
            $repaired_word = $word;
            if (ctype_upper($repaired_word)) {
                $repaired_word = mb_strtolower($word, 'UTF-8');
            }
            $newArr[$i] = $repaired_word;
            $text_len = $text_len + $word_len;
            $i++;
        } else {
            $newArr[$i] = ' ...';
            break;
        }
    }

    $dealText = implode(' ', $newArr);
}

$dnes = Date("Y-m-d");
$datum = date("Y-m-d", strtotime($model->DStart));
$deal_class = '';

if ($dnes == $datum) {
    $deal_class = '';
} else {
    $deal_class = ' deal-blue';
}

if ($days < 1) {
    $deal_class = ' deal-red';
}

$cookie_city_name = \Yii::$app->getRequest()->getCookies()->getValue('cnm');
$cookie_city_id = \Yii::$app->getRequest()->getCookies()->getValue('cid');

if (!empty($cookie_city_id) && !empty($cookie_city_name)) {

    $city_id = $cookie_city_id;
    $city_name = $cookie_city_name;
} else {

    $city_id = $model->cities[0]->CityId;
    $city_name = $model->cities[0]->Name;
}
?>
<div class="deal-container">
    <div class="deal<? echo $deal_class; ?>">
        <div class="deal-discount">-<? echo $model->Discount; ?>% </div>
        <div class="deal-img" style="background:url(<? echo $model->OriginalImage; ?>) center center no-repeat;background-size: cover;">

            <?
            /*if (!empty($model->FeedKws) && !Yii::$app->user->isGuest) {
                ?>
                <div class="widget text-right">
                    <? echo $model->FeedKws; ?>
                </div>

            <? }*/
            ?>

            <div class="deal-time">
                <?
                if ($days < 0) {
                    echo '<s><span class=\"red\">' . date("j.n.Y", strtotime($model->DEnd)) . '</span></s>';
                } else {
                    if ($days > 1) {
                        echo "<span class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></span> <span class=\"timeleft\">" . $daysf . "d:" . $hoursf . "h:" . $minutes . "m</span>";
                    } else {
                        echo "<span class=\"glyphicon glyphicon-time\" aria-hidden=\"true\"></span> <span class=\"red timeleft\">" . $daysf . "d:" . $hoursf . "h:" . $minutes . "m</span>";
                    }
                }
                ?>
            </div>
            <div class="product-social">
                <a href="https://www.facebook.com/sharer/sharer.php?u=http://www.meslevy.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-facebook" title="Sdílet přes Facebook"><i class="fa fa-facebook"></i></a>
                <a href="https://twitter.com/home?status=Bezva%20sleva%20:)%0Ahttp://www.meslevy.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-twitter" title="Tweetnout"><i class="fa fa-twitter"></i></a>
                <a href="https://plus.google.com/share?url=http://www.meslevy.cz/slevy/view?id=<? echo $model->DealId; ?>" target="_blank" rel="nofollow" class="btn btn-social-icon btn-google-plus" title="Sdílet přes G+"><i class="fa fa-google-plus"></i></a>
            </div>
            <div class="caption">
                <div class="row">
                    <div class="col-sm-12">
                        <a class="btn btn-default" href="<? echo Yii::$app->urlManager->createUrl(['slevy/view', "id" => $model->DealId]); ?>" role="button">Detail</a>&nbsp;
                        <a class="btn btn-success linkout" data-href="<? echo Yii::$app->urlManager->createUrl(['papa/go', "id" => $model->DealId]); ?>" role="button" target="_blank" rel="nofollow">K PRODEJCI</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="deal-text">
            <a href="<? echo Yii::$app->urlManager->createUrl(['slevy/view', "id" => $model->DealId]); ?>"><? echo Html::encode($dealText); ?></a>
        </div>
        <div class="deal-stats row">
            <div class="col-sm-4 col-xs-6 text-center">
                <span class="deal-price"><? echo $model->FPrice; ?></span> Kč 
            </div>
            <div class="col-sm-3 col-xs-6 text-center">
                <span class="deal-oprice"><? echo $model->OPrice; ?> Kč </span>
            </div>
            <div class="col-sm-5 col-xs-12 text-center">
                <img src="http://images.meslevy.cz/loga/<? echo $model->server->Logo; ?>" class="deal-logo" alt="<? echo $model->server->Name; ?>" />
            </div>
        </div>

    </div>

</div>
