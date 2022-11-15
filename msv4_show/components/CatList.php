<?php

namespace app\components;

use yii\base\Widget;
use yii\data\ActiveQuery;
use app\models\Kategorie;
use app\models\Slevy;
use yii;

class CatList extends Widget {

    private $city;
    private $current_cat;
    private $server;

    public function init() {
        parent::init();
    }

    public function run() {

        $this->city = 6252;
        $this->server = '';

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

        if (!empty($_GET['w']) && is_numeric($_GET['w'])) {
            $this->server = $_GET['w'];
        }

        $candidates = array();
        $categories = array();

        if (!empty($_GET['k']) && is_numeric($_GET['k'])) {
            $candidates = Kategorie::find()->where(['rodic' => $_GET['k']])->orderBy('jmeno')->asArray()->all();
            $this->current_cat = Kategorie::find()->where(['id' => $_GET['k']])->asArray()->one();
            if (!empty($candidates)) {
                $categories = $this->check_candidates($candidates, $city_part);
            }
            if (empty($categories)) {
                $candidates = Kategorie::find()->where(['rodic' => $this->current_cat['rodic']])->orderBy('jmeno')->asArray()->all();
                $categories = $this->check_candidates($candidates, $city_part);
            }
        } else {
            $candidates = Kategorie::find()->where(['rodic' => 0])->orderBy('jmeno')->asArray()->all();
            $categories = $this->check_candidates($candidates, $city_part);
        }

        return $this->render('cat_list', array('categories' => $categories, 'city' => $this->city, 'current' => $this->current_cat, 'server' => $this->server));
    }

    protected function check_candidates($candidates, $city_part) {
        $categories = array();
        foreach ($candidates as $candidate) {
            $deals_count = Slevy::find()
                    ->leftjoin('SlevyMesta', 'SlevyMesta.DealId = Slevy.DealId')
                    ->leftjoin('SlevyKategorie', 'SlevyKategorie.sleva_id = Slevy.DealId')
                    ->where('SlevyKategorie.kategorie_id = :cid AND Slevy.Status = 2 AND Slevy.Rating >= ' . Yii::$app->params['avg_rating'] . ' AND SlevyMesta.CityId IN (6252' . $city_part . ')', [':cid' => $candidate['id']])
                    ->groupBy('Slevy.Text')
                    ->count();
            if ($deals_count > 0) {
                $categories[] = $candidate;
            }
        }
        return($categories);
    }

}
