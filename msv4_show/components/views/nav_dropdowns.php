<?
use kartik\widgets\Typeahead;

$city_button_class = 'btn-green';
$city_name = Yii::$app->params['city_name'];
$city_id = Yii::$app->params['city_id'];
$city_clearname = '';
$curl = Yii::$app->urlManager->createUrl(['slevy/index']);

$items = array();
foreach ($cities as $item) {
    if (count($item['cities']) > 0) {
        $items[] = ['item' => $item['Province'], 'type' => 'p'];
        foreach ($item['cities'] as $i) {
            $items[] = ['item' => $i, 'type' => 'c'];
        }
    }
}
?>
<div class="city-menu">
    <ul class="nav navbar-nav navbar-right">
        <li class="dropdown mega-dropdown">
            <a id="city-btn" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="glyphicon glyphicon-map-marker"></span> <? echo $city_name; ?> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu mega-dropdown-menu row">
                <?
                if (!empty($items)) {
                    $k = 0;
                    foreach ($items as $it) {
                        $k++;
                        if ($k == 10)
                            $k = 1;
                        if ($k == 1) {
                            echo '<li class="col-sm-2">'."\n"
                            . '<ul>'."\n";
                        }
                        if($it['type'] === 'p'){
                            echo '<li class="dropdown-header">'.$it['item'].'</li>'."\n";
                        }else{
                            if($it['item']['city_residents'] > 100000){
                                echo '<li><a href="' . Yii::$app->urlManager->createUrl(['slevy/index', 'm' => $it['item']['city_id']]) . '"><strong><span class="black">' . $it['item']['city_name'] . '</span></strong></a></li>'."\n";
                            }else{
                                echo '<li><a href="' . Yii::$app->urlManager->createUrl(['slevy/index', 'm' => $it['item']['city_id']]) . '">' . $it['item']['city_name'] . '</a></li>'."\n";
                            }
                            
                            
                        }
                        if ($k == 9) {
                            echo '</ul>'."\n"
                            . '</li>'."\n";
                        }
                    }
                }
                ?>
            </ul><!-- dropdown-menu mega-dropdown-menu row -->
        </li> <!-- dropdown mega-dropdown -->
    </ul> <!-- nav navbar-nav navbar-right -->
</div> <!-- city-menu -->

<? //echo Yii::$app->controller->action->id;   ?>

