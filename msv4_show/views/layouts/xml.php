<?php

   Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
   $headers = Yii::$app->response->headers;
   $headers->add('Content-Type', 'text/xml');

?>

<?= $content ?>

