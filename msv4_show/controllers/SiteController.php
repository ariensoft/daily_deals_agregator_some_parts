<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Kategorie;
use app\models\Slevy;
use app\models\Cities;
use yii\data\ActiveQuery;
use yii\helpers\Html;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact() {
        $this->layout = 'main';
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
                    'model' => $model,
        ]);
    }

    public function actionAbout() {
        return $this->render('about');
    }

    public function actionSitemap() {
        $this->layout = 'xml';
        $urls = [];
        $home = 'http://www.meslevy.cz';
        $date = Date("Y-m-d", Time());
        $changefreq = 'daily';
        $priority = 1;

        $urls[] = [
            'loc' => $home,
            'lastmod' => $date,
            'changefreq' => 'hourly',
            'priority' => $priority,
        ];

        $cities_query = (new \yii\db\Query())
                //->select('Deals.CityId as id, Cities.Name as name, Count(*) as count')
                ->select('SlevyMesta.CityId as id, Cities.Name as name, Cities.Province as province, Cities.Residents as residents')
                ->from('Cities')
                ->leftjoin('SlevyMesta', 'SlevyMesta.CityId = Cities.CityId')
                ->leftJoin('Slevy', 'Slevy.DealId = SlevyMesta.DealId')
                ->where("Cities.CityId!=6253 AND Slevy.Status=2 AND Slevy.Rating >= " . Yii::$app->params['avg_rating'])
                ->groupBy('Cities.Name')
                ->orderBy(['Cities.Residents' => SORT_DESC])
                //->limit(20)
                ->all();
        $slevy_ids = [];
        foreach ($cities_query as $city) {
            $urls[] = [
                'loc' => $home . Yii::$app->urlManager->createUrl(['slevy/index', "m" => $city['id']]),
                'lastmod' => $date,
                'changefreq' => 'hourly',
                'priority' => 0.9,
            ];
            $slevy = Slevy::find()
                    ->joinWith(['cities', 'categories'])
                    ->Where(['Slevy.Status' => 2])
                    ->andwhere(['SlevyMesta.CityId' => $city['id']])
                    ->andwhere(['>=', 'Slevy.Rating', Yii::$app->params['avg_rating']])
                    ->orderBy('Slevy.DStart DESC')
                    ->all();
            $cat_urls = [];
            foreach ($slevy as $sleva) {
                $slevy_ids[$sleva->DealId] = ['id' => $sleva->DealId, 'start' => $sleva->DStart];
                if (!empty($sleva->categories)) {
                    foreach ($sleva->categories as $category) {
                        if (!in_array($category->id, $cat_urls)) {
                            $cat_urls[] = $category->id;
                            reset($cat_urls);
                        }
                    }
                }
            }
            foreach ($cat_urls as $cat_id) {
                $urls[] = [
                    'loc' => Html::encode($home . Yii::$app->urlManager->createUrl(['slevy/index', "m" => $city['id'], "k" => $cat_id])),
                    'lastmod' => $date,
                    'changefreq' => 'daily',
                    'priority' => 0.8,
                ];
            }
            unset($cat_urls);
        }
        foreach ($slevy_ids as $slevy_id) {
            $urls[] = [
                'loc' => $home . Yii::$app->urlManager->createUrl(['slevy/view', "id" => $slevy_id['id']]),
                'lastmod' => $slevy_id['start'],
                'changefreq' => 'daily',
                'priority' => 0.5,
            ];
        }
        unset($slevy_ids);
        return $this->render('sitemap', [
                    'urls' => $urls
        ]);
    }

}
