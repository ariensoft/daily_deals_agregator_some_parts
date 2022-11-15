<?
if (!empty($categories)) {
    ?>
    <div class="widget">    
        <div class="widget-title">
            <?
            if (!empty($current)) {
                echo '<h2>' . $current['jmeno'] . '</h2>';
            } else {
                echo '<h2>Kategorie</h2>';
            }
            ?>
        </div>
        <ul class="nav nav-sidebar">
            <?
            foreach ($categories as $category) {
                ?>
                <li>

                    <a href="<?
                    echo Yii::$app->urlManager->createUrl(['slevy/index', 'm' => $city, 'k' => $category['id']]);
                    ?>"<?
                       if ($current['id'] == $category['id']) {
                           echo ' class="active"';
                       }
                       ?>>

        <? echo $category['jmeno']; ?>

                    </a>

                </li>           
    <? } ?>
        </ul>
    </div>
<? }
?>



