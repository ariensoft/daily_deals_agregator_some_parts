<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use app\models\Slevy;
use yii;

//use yii\data\ActiveQuery;

class RelatedDeals extends Widget {

    public $id;
    public $cid;
    public $cnm;
    public $tags;
    public $data;
    public $found;
    public $diff = 0;
    public $cities;

    public function init() {
        parent::init();

        if (!empty($this->tags)) {

            $tags = array_reverse($this->tags);
            $this->cities[] = 6252;
            $this->cities[] = $this->cid;

            foreach ($tags as $tag) {
                $count = Slevy::find()
                        ->joinWith(['categories', 'cities', 'server'])
                        ->where('SlevyKategorie.kategorie_id = :cat AND Slevy.Status = 2 AND Slevy.Rating >= :r AND Slevy.DealId != :did', [
                            ':cat' => $tag['id'],
                            ':r' => Yii::$app->params['avg_rating'],
                            ':did' => $this->id
                        ])
                        ->andwhere(['in', 'SlevyMesta.CityId', $this->cities])
                        ->groupBy('Slevy.Text')
                        ->count();

                if ($count > 0) {
                    $this->data = Slevy::find()
                            ->joinWith(['categories', 'cities', 'server'])
                            ->where('SlevyKategorie.kategorie_id = :cat AND Slevy.Status = 2 AND Slevy.Rating >= :r AND Slevy.DealId != :did', [
                                ':cat' => $tag['id'],
                                ':r' => Yii::$app->params['avg_rating'],
                                ':did' => $this->id
                            ])
                            ->andwhere(['in', 'SlevyMesta.CityId', $this->cities])
                            ->groupBy('Slevy.Text')
                            ->orderBy('Slevy.Rating DESC')
                            ->limit(6)
                            ->all();
                    $this->found = $tag['id'];
                    $this->diff = $count - 6;
                    break;
                }
            }
            /* $query = (new \yii\db\Query())
              ->select('
              Slevy.DealId as DealId,
              Slevy.Text as Text,
              Slevy.FPrice as FPrice,
              Slevy.OriginalImage as Image,
              Slevy.DEnd as DEnd,
              Slevy.DStart as DStart,
              Slevy.Discount as Discount,
              Slevy.Rating as Rating,
              Servers.Name as ServerName,
              Servers.Insurance as Insurance
              ')
              ->from('Deals')
              ->leftjoin('Servers', 'Slevy.ServerId = Servers.ServerId')
              ->leftjoin('DealCities', 'DealCities.DealId = Slevy.DealId')
              ->where($tag_parts)
              //->where("CityId = $this->cid AND Tags RLIKE '[[:<:]]$this->tags[[:>:]]' AND Status = 2 AND DealId != $this->id")
              ->groupBy('Slevy.Text')
              ->orderBy(['Slevy.Rating' => SORT_DESC])
              ->limit(12)
              ->all();

              $this->data = $query; */
        }
    }

    public function run() {

        return $this->render('related_deals', array('data' => $this->data, 'found' => $this->found, 'diff' => $this->diff, 'cities' => $this->cities));
    }

}

?>
