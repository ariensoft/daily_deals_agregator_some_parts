<?php

namespace app\components;

use yii\base\Widget;
use yii\data\ActiveQuery;
use app\models\Kategorie;
use app\models\Slevy;
use yii;

class MainSearchForm extends Widget {

    private $city;
    private $city_name;
    private $category;

    public function init() {
        parent::init();
    }

    public function run() {

        $this->city = Yii::$app->params['city_id'];
        $this->city_name = Yii::$app->params['city_name'];

        $cities_query = (new \yii\db\Query())
                //->select('Deals.CityId as id, Cities.Name as name')
                ->select('SlevyMesta.CityId as id, Cities.Name as name, Cities.Province as province, Cities.Residents as residents')
                ->from('Cities')
                ->leftjoin('SlevyMesta', 'SlevyMesta.CityId = Cities.CityId')
                ->leftJoin('Slevy', 'Slevy.DealId = SlevyMesta.DealId')
                ->where("Cities.CityId!=6253 AND Slevy.Status=2 AND Slevy.Rating >= ".Yii::$app->params['avg_rating'])
                ->groupBy('Cities.Name')
                ->orderBy(['Cities.Residents' => SORT_DESC])
                //->limit(20)
                ->all();

            $candidates = Kategorie::find()->where(['rodic' => 0])->orderBy('priorita','jmeno')->asArray()->all();
            $categories = $this->check_candidates($candidates, $this->city);

        return $this->render('main_search_form', array('cities' => $cities_query, 'categories' => $categories, 'city' => $this->city, 'category' => $this->category));
    }

        protected function check_candidates($candidates, $city_part) {
        $categories = array();
        foreach ($candidates as $candidate) {
            $deals_count = Slevy::find()
                    ->leftjoin('SlevyMesta', 'SlevyMesta.DealId = Slevy.DealId')
                    ->leftjoin('SlevyKategorie', 'SlevyKategorie.sleva_id = Slevy.DealId')
                    ->where('SlevyKategorie.kategorie_id = :cid AND Slevy.Status = 2 AND Slevy.Rating > '.Yii::$app->params['avg_rating'].' AND SlevyMesta.CityId IN (6252,' . $city_part . ')', [':cid' => $candidate['id']])
                    ->groupBy('Slevy.Text')
                    ->count();
            if ($deals_count > 0) {
                $categories[] = $candidate;
            }
        }
        return($categories);
    }
}
