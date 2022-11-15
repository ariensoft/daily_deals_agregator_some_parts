<?php

namespace app\components;

use yii\base\Widget;
use yii\data\ActiveQuery;
use app\models\Kategorie;
use app\models\Slevy;

class NavMegamenu extends Widget {

    private $city;

    public function init() {
        parent::init();
    }

    public function run() {

        $this->city = 6252;

        if (!empty($_GET['m']) && is_numeric($_GET['m'])) {
            $this->city = $_GET['m'];
            $city_part = ',' . $_GET['m'];
        } else {
            $city_part = '';
            $cookie_city_id = \Yii::$app->getRequest()->getCookies()->getValue('m');

            if (!empty($cookie_city_id) && is_numeric($cookie_city_id)) {
                $this->city = $cookie_city_id;
                $city_part = ',' . $cookie_city_id;
            }
        }



        $parrents = Kategorie::find()->where(['rodic' => 0])->orderBy('priorita')->asArray()->all();

        foreach ($parrents as $parrent_id => $parrent) {
            $deals = Slevy::find()
                    ->with('server')
                    ->leftjoin('SlevyMesta', 'SlevyMesta.DealId = Slevy.DealId')
                    ->leftjoin('SlevyKategorie', 'SlevyKategorie.sleva_id = Slevy.DealId')
                    ->where('SlevyKategorie.kategorie_id = :cid AND Slevy.Status = 2 AND Slevy.Rating > 0 AND SlevyMesta.CityId IN (6252' . $city_part . ')', [':cid' => $parrent['id']])
                    ->groupBy('Slevy.Text')
                    ->orderBy(['Slevy.Rating' => SORT_DESC])
                    ->limit(3)
                    ->asArray()
                    ->all();

            $parrents[$parrent_id]['deals'] = $deals;
            $subs_arr = [];
            $subs = Kategorie::find()->where(['rodic' => $parrent['id']])->asArray()->all();
            if (!empty($subs)) {
                foreach ($subs as $sub) {
                    $sub_deal = Slevy::find()
                            //->with('server')
                            ->leftjoin('SlevyMesta', 'SlevyMesta.DealId = Slevy.DealId')
                            ->leftjoin('SlevyKategorie', 'SlevyKategorie.sleva_id = Slevy.DealId')
                            ->where('SlevyKategorie.kategorie_id = :cid AND Slevy.Status = 2 AND Slevy.Rating > 0 AND SlevyMesta.CityId IN (6252' . $city_part . ')', [':cid' => $sub['id']])
                            ->groupBy('Slevy.Text')
                            ->orderBy(['Slevy.Rating' => SORT_DESC])
                            ->limit(1)
                            ->asArray()
                            ->all();
                    if (!empty($sub_deal)) {
                        $subs_arr[] = [
                            'id' => $sub['id'],
                            'jmeno' => $sub['jmeno'],
                            'url' => $sub['url'],
                            'img' => $sub_deal[0]['Image'],
                        ];
                    }
                }
            }
            $parrents[$parrent_id]['subs'] = $subs_arr;
        }

        return $this->render('nav_megamenu', array('data' => $parrents, 'city' => $this->city));
    }

}
