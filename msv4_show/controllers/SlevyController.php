<?php

namespace app\controllers;

use Yii;
use app\models\Slevy;
use app\models\Kategorie;
use app\models\SlevyMesta;
use app\models\Cities;
use app\models\SlevySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\Cookie;

/**
 * SlevyController implements the CRUD actions for Slevy model.
 */
class SlevyController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Slevy models.
     * @return mixed
     */
    public function actionIndex($m = '', $k = '', $s = '', $w = '') {

        $admin = 0;
        if (!Yii::$app->user->isGuest) {
            $admin = 1;
        }
        $cats = Kategorie::find()->asArray()->all();
        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat ["id"]] = array(
                'id' => $cat ["id"],
                'parent' => $cat ["rodic"],
                'name' => $cat ["jmeno"],
            );
        }
        $cat_tree = Kategorie::buildTree($categories);
        $parrent_info = [];
        $city_id = Yii::$app->params['city_id'];
        $city_name = Yii::$app->params['city_name'];
        $cities[] = 6252;
        $query = Slevy::find()->joinWith(['cities', 'categories'])->groupBy('Slevy.Text');
        $query->Where(['Status' => 2]);
        $query->andWhere(['>=', 'Rating', Yii::$app->params['avg_rating']]);

        if (!empty($m) && is_numeric($m)) {
            $cities[] = (int) $m;
            $city_id = (int) $m;
            $cid_cookie = new Cookie([
                'name' => 'm',
                'value' => (int) $m,
                'expire' => time() + 86400 * 365,
                'httpOnly' => true,
            ]);
            \Yii::$app->getResponse()->getCookies()->add($cid_cookie);

            $city = Cities::find()->where(['CityId' => $city_id])->one();
            $city_name = $city->Name;
            $name_cookie = new Cookie([
                'name' => 'mn',
                'value' => $city_name,
                'expire' => time() + 86400 * 365,
                'httpOnly' => true,
            ]);
            \Yii::$app->getResponse()->getCookies()->add($name_cookie);
        } else {
            $cookie_city_id = \Yii::$app->getRequest()->getCookies()->getValue('m');
            $cookie_city_name = \Yii::$app->getRequest()->getCookies()->getValue('mn');

            if (!empty($cookie_city_id)) {
                $cities[] = (int) $cookie_city_id;
                $city_id = (int) $cookie_city_id;
                $city_name = $cookie_city_name;
            }

            //\yii::$app->response->cookies->remove('cid');
            //\yii::$app->response->cookies->remove('cnm');
        }

        Yii::$app->params['city_id'] = $city_id;
        Yii::$app->params['city_name'] = $city_name;
        $query->andwhere(['in', 'SlevyMesta.CityId', $cities]);
        //$query->andwhere(['SlevyMesta.CityId' => [6252, 346]]);

        if (!empty($k) && is_numeric($k)) {
            $res[] = $k;
            $this->find_childs($k, $cat_tree, $res);
            $query->andWhere(['SlevyKategorie.kategorie_id' => $res]);
            $parrent_ids = Kategorie::find_parrents($k, $categories);
            if (!empty($parrent_ids)) {
                foreach ($parrent_ids as $parrent_id) {
                    $parrent_info[$parrent_id] = $categories[$parrent_id];
                }
            }
        }

        if (strlen($s) > 0) {
            $this->layout = 'main';
            $stemmer = \Yii::$app->getModule('stemmer');
            $stemmed_text = $stemmer->cz_stem($s, FALSE);
            $hleda_se = trim($stemmed_text);
            $exp = explode(" ", $stemmed_text);
            foreach ($exp as $word) {

                if (mb_strlen($word) > 0) {

                    $searched[] = $word;
                    $searchw = "M" . $word;

                    //$criteria->addSearchCondition('Text', $w, true, 'AND');
                    //$criteria->addSearchCondition('SearchText', $searchw, true, 'AND');
                    $query->andWhere(['LIKE', 'Slevy.SearchText', $searchw]);
                    //$criteria->addCondition('Text regexp :regexp', 'AND');
                    //$criteria->params = array(':regexp' => '[[:<:]]' . $word . '');
                }
            }
        }

        if (!empty($w) && is_numeric($w)) {
            $query->andWhere(['Slevy.ServerId' => $w]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 24,
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['DStart' => SORT_DESC, 'Rating' => SORT_DESC],
            //'defaultOrder' => ['Rating' => SORT_DESC],
            'attributes' => [
                'Discount' => [
                    'label' => 'Sleva',
                    'default' => SORT_DESC
                ],
                'FPrice' => [
                    'label' => 'Cena',
                    'default' => SORT_ASC
                ],
                'DStart' => [
                    'label' => 'Zveřejněno',
                    'default' => SORT_DESC
                ],
                'DEnd' => [
                    'label' => 'Zbývající čas',
                    'default' => SORT_ASC
                ],
                'Rating' => [
                    'label' => 'Popularita',
                    'default' => SORT_DESC
                ],
            ]
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'parrent_info' => $parrent_info,
                    'admin' => $admin,
        ]);
        /*
          $searchModel = new SlevySearch();
          $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

          return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
          ]); */
    }

    /**
     * Displays a single Slevy model.
     * @param integer $id
     * @return mixed
     */
    public function actionNovinky() {
        $this->layout = 'main';
        $cats = Kategorie::find()->asArray()->all();
        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat ["id"]] = array(
                'id' => $cat ["id"],
                'parent' => $cat ["rodic"],
                'name' => $cat ["jmeno"],
            );
        }
        $cat_tree = Kategorie::buildTree($categories);
        $parrent_info = [];
        $city_id = Yii::$app->params['city_id'];
        $city_name = Yii::$app->params['city_name'];
        $cities[] = 6252;
        $cities[] = $city_id;
        $query = Slevy::find()->joinWith(['cities', 'categories'])->groupBy('Slevy.Text');
        $query->Where(['Status' => 2]);
        $query->andWhere(['>=', 'Rating', Yii::$app->params['avg_rating']]);
        $query->andwhere(['in', 'SlevyMesta.CityId', $cities]);
        $query->andWhere(['DStart' => (string) Date("Y-m-d", Time())]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 24,
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['DStart' => SORT_DESC, 'Rating' => SORT_DESC],
            'attributes' => [
                'Discount' => [
                    'label' => 'Sleva',
                    'default' => SORT_DESC
                ],
                'FPrice' => [
                    'label' => 'Cena',
                    'default' => SORT_ASC
                ],
                'DStart' => [
                    'label' => 'Zveřejněno',
                    'default' => SORT_DESC
                ],
                'DEnd' => [
                    'label' => 'Zbývající čas',
                    'default' => SORT_ASC
                ],
                'Rating' => [
                    'label' => 'Popularita',
                    'default' => SORT_DESC
                ],
            ]
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'parrent_info' => $parrent_info
        ]);
    }

    public function actionSlevogedon() {
        $this->layout = 'main';
        $cats = Kategorie::find()->asArray()->all();
        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat ["id"]] = array(
                'id' => $cat ["id"],
                'parent' => $cat ["rodic"],
                'name' => $cat ["jmeno"],
            );
        }
        $cat_tree = Kategorie::buildTree($categories);
        $parrent_info = [];
        $city_id = Yii::$app->params['city_id'];
        $city_name = Yii::$app->params['city_name'];
        $cities[] = 6252;
        $cities[] = $city_id;
        $query = Slevy::find()->joinWith(['cities', 'categories'])->groupBy('Slevy.Text');
        $query->Where(['Status' => 2]);
        $query->andWhere(['>=', 'Rating', Yii::$app->params['avg_rating']]);
        $query->andwhere(['in', 'SlevyMesta.CityId', $cities]);
        $query->andWhere(['>=', 'DEnd', (string) Date("Y-m-d", Time())]);
        $query->andWhere(['<=', 'DEnd', (string) Date("Y-m-d", Time()) . ' 23:59:59']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 24,
            ],
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['DEnd' => SORT_ASC, 'Rating' => SORT_DESC],
            'attributes' => [
                'Discount' => [
                    'label' => 'Sleva',
                    'default' => SORT_DESC
                ],
                'FPrice' => [
                    'label' => 'Cena',
                    'default' => SORT_ASC
                ],
                'DStart' => [
                    'label' => 'Zveřejněno',
                    'default' => SORT_DESC
                ],
                'DEnd' => [
                    'label' => 'Zbývající čas',
                    'default' => SORT_ASC
                ],
                'Rating' => [
                    'label' => 'Popularita',
                    'default' => SORT_DESC
                ],
            ]
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'parrent_info' => $parrent_info
        ]);
    }

    public function actionTop24() {
        $this->layout = 'main';
        $cats = Kategorie::find()->asArray()->all();
        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat ["id"]] = array(
                'id' => $cat ["id"],
                'parent' => $cat ["rodic"],
                'name' => $cat ["jmeno"],
            );
        }
        $cat_tree = Kategorie::buildTree($categories);
        $parrent_info = [];
        $city_id = Yii::$app->params['city_id'];
        $city_name = Yii::$app->params['city_name'];
        $cities[] = 6252;
        $cities[] = $city_id;
        $query = Slevy::find()->joinWith(['cities', 'categories'])->groupBy('Slevy.Text')->limit(24);
        $query->Where(['Status' => 2]);
        $query->andWhere(['>=', 'Rating', Yii::$app->params['avg_rating']]);
        $query->andwhere(['in', 'SlevyMesta.CityId', $cities]);
        $query->andWhere(['>=', 'DStart', (string) Date("Y-m-d", strtotime("-7 days"))]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $dataProvider->setSort([
            'defaultOrder' => ['Rating' => SORT_DESC],
            'attributes' => [
                'Discount' => [
                    'label' => 'Sleva',
                    'default' => SORT_DESC
                ],
                'FPrice' => [
                    'label' => 'Cena',
                    'default' => SORT_ASC
                ],
                'DStart' => [
                    'label' => 'Zveřejněno',
                    'default' => SORT_DESC
                ],
                'DEnd' => [
                    'label' => 'Zbývající čas',
                    'default' => SORT_ASC
                ],
                'Rating' => [
                    'label' => 'Popularita',
                    'default' => SORT_DESC
                ],
            ]
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'parrent_info' => $parrent_info
        ]);
    }

    public function actionView($id) {
        $this->layout = 'main';
        $cats = Kategorie::find()->asArray()->all();
        $categories = [];
        foreach ($cats as $cat) {
            $categories[$cat ["id"]] = array(
                'id' => $cat ["id"],
                'parent' => $cat ["rodic"],
                'name' => $cat ["jmeno"],
            );
        }
        $model = $this->findModel($id);
        $main_category = 0;
        $deal_categories = [];
        $cat_tree = [];
        $catys = [];
        if (count($model->categories) > 0) {
            foreach ($model->categories as $category) {
                foreach ($categories as $key_categories => $value_categories) {
                    if ((int) $category->id == $value_categories['id']) {
                        $deal_categories[$value_categories['id']] = $value_categories;
                    }
                }
            }
            $cat_tree = Kategorie::buildTree($deal_categories);

            $catys = Kategorie::tree_to_arr($cat_tree, $cat_tree[0]['id']);
        }
        return $this->render('view_xs12', [
                    'model' => $model,
                    'cats' => $catys,
        ]);
    }

    public function actionXml($city_id = 346) {
        $this->layout = 'xml';
        $cities[] = 6252;
        $cities[] = $city_id;
        $query = Slevy::find()
                ->joinWith(['cities', 'categories'])
                ->groupBy('Slevy.Text')
                ->Where(['Status' => 2])
                ->andwhere(['in', 'SlevyMesta.CityId', $cities])
                ->orderBy('DStart DESC')
                //->limit(200)
                ->all();

        return $this->render('xml_index', [
                    'data' => $query
        ]);
    }

    public function actionFkw() {
        $this->layout = 'main';
        $query = Slevy::find()
                ->joinWith(['server',])
                ->groupBy('Slevy.FeedKws')
                ->orderBy('Servers.Name ASC')
                //->limit(20)
                ->all();

        return $this->render('fkw_index', [
                    'data' => $query
        ]);
    }

    /**
     * Finds the Slevy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Slevy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Slevy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function find_childs($cid, $arr, &$res) {

        foreach ($arr as $k => $v) {
            if ($v['parent'] == $cid) {
                $res[] = $v['id'];
            }
            if (isset($v['childs'])) {
                $this->find_childs($cid, $v['childs'], $res);
            }
        }
    }

}
