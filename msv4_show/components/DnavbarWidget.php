<?php

namespace app\components;

use yii\base\Widget;
use yii\data\ActiveQuery;
use yii;

class DnavbarWidget extends Widget {

    //public $message;

    public function init() {
        parent::init();
    }

    public function run() {

        $cities = array();

        $cities_query = (new \yii\db\Query())
                //->select('Deals.CityId as id, Cities.Name as name, Count(*) as count')
                ->select('SlevyMesta.CityId as id, Cities.Name as name, Cities.Province as province, Cities.Residents as residents')
                ->from('Cities')
                ->leftjoin('SlevyMesta', 'SlevyMesta.CityId = Cities.CityId')
                ->leftJoin('Slevy', 'Slevy.DealId = SlevyMesta.DealId')
                ->where("Cities.CityId!=6253 AND Slevy.Status=2 AND Slevy.Rating >= ".Yii::$app->params['avg_rating'])
                ->groupBy('Cities.Name')
                ->orderBy(['Cities.Residents' => SORT_DESC])
                //->limit(20)
                ->all();


        $province_query = (new \yii\db\Query())
                ->select('Province')
                ->from('Cities')
                ->where("CityId NOT IN ( 6252, 6253 )")
                ->groupBy('Province')
                ->orderBy(['Province' => SORT_ASC])
                ->all();

        foreach ($province_query as $province) {
            $subgroups = array();
            foreach ($cities_query as $city) {
                if ($city['province'] == $province['Province']) {
                    $subgroups[] = ['city_id' => $city['id'], 'city_name' => $city['name'], 'city_residents' => $city['residents']];
                }
            }
            $cities[] = ['Province' => $province['Province'], 'cities' => $subgroups];
            unset($subgroups);
        }


        return $this->render('nav_dropdowns', array('cities' => $cities));
    }

}

?>
