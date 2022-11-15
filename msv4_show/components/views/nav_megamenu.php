<? if (!empty($data)) { ?>
<nav>
    <ul class="nav nav-justified">
        <? foreach ($data as $mc) { ?>
            <? if (!empty($mc['deals'])) { ?>
                <li class="dropdown mega-dropdown">
                    <a href="<? echo Yii::$app->urlManager->createUrl(['slevy/index', "m" => $city, "k" => $mc['id']]); ?>" class="dropdown-toggle" data-toggle="dropdown"><? echo $mc['jmeno']; ?></a>
                    
                    <ul class="dropdown-menu mega-dropdown-menu row">
                        <li class="col-sm-4 tiles-left">
                            <ul>
                                <div id="mega-carousel" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?
                                        $c = 0;
                                        foreach ($mc['deals'] as $deal) {
                                            ?>
                                            <div class="item<? if ($c == 0) echo ' active';; ?>">
                                                <div class="mega-discount">-<? echo $deal['Discount']; ?>% </div>
                                                <div class="mega-img" style="background:url(<? echo $deal['OriginalImage']; ?>) center center no-repeat;background-size: cover;">
                                                    <div class="mega-text">
                                                        <?
                                                        $dealText = $deal['Text'];

                                                        if (mb_strlen($dealText, 'UTF-8') > 90) {

                                                            $wordsArr = explode(' ', $deal['Text']);
                                                            $i = 0;
                                                            $text_len = 0;
                                                            foreach ($wordsArr as $word) {
                                                                $word_len = mb_strlen($word, 'UTF-8');
                                                                if ($text_len <= 90) {
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
                                                        ?>
                                                        <? echo $dealText; ?>
                                                    </div>
                                                </div>

                                            </div><!-- End Item -->
                                            <?
                                            $c++;
                                            unset($wordsArr);
                                            unset($newArr);
                                        }
                                        ?>
                                    </div><!-- End Carousel Inner -->
                                </div><!-- /.carousel -->

                                <li class="divider"></li>
                                <li><a href="<? echo Yii::$app->urlManager->createUrl(['slevy/index', "m" => $city, "k" => $mc['id']]); ?>">Vše z kategorie <span class="glyphicon glyphicon-chevron-right pull-right"></span></a></li>
                            </ul>
                        </li>
                        <li class="col-sm-8 tiles-right">

                            <div class="row">
                                <?
                                if (!empty($mc['subs'])) {
                                    foreach ($mc['subs'] as $sub) {
                                        ?>
                                        <a href="<? echo Yii::$app->urlManager->createUrl(['slevy/index', "m" => $city, "k" => $sub['id']]); ?>">
                                            <div class="col-md-3 col-sm-6 col-xs-12">
                                                <div class="mega-item" style="background:url(<? echo $sub['img']; ?>) center center no-repeat;background-size: cover;">
                                                    <div class="mega-item-text"><? echo $sub['jmeno']; ?></div>
                                                </div>
                                            </div>
                                        </a>
                                    <? } ?>

                                <? } ?>
                            </div>
                        </li>
                    </ul>
                   
                </li>
            <? } ?>
        <? }
        ?>
    </ul>
</nav>
<? } ?>
