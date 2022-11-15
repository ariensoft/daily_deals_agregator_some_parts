<?php

foreach ($data as $item) {
    echo '<div>'.$item->server->Name.': '.$item->FeedKws.'</div>';
}
