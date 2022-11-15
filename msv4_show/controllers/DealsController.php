<?php

namespace app\controllers;

use Yii;
use app\models\Deals;
use app\models\Slevy;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Cookie;

/**
 * DealsController implements the CRUD actions for Deals model.
 */
class DealsController extends Controller {

    public $sid_category;
    public $tag_info;

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
     * Lists all Deals models.
     * @return mixed
     */
    public function actionIndex($cid = '', $gid = '', $s = '', $cnm = '', $tag = '', $sid = '') {
        throw new NotFoundHttpException('Stránka již neexistuje!');
    }

    /**
     * Displays a single Deals model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $deal = $this->findModel($id);
        $redirId = 0;
        if ($deal) {
            $ownerid = $deal->OwnerId;
            $redirDeal = Slevy::find()
                    ->where(['OwnerId' => $deal->OwnerId])
                    ->one();
            if ($redirDeal) {
                $redirId = $redirDeal->DealId;
                $this->redirect(Yii::$app->urlManager->createUrl(['slevy/view', "id" => $redirId]), 301);
            } else {
                throw new \yii\web\NotFoundHttpException('Stránka již neexistuje!');
            }
        }
        return FALSE;
    }

    /**
     * Finds the Deals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Deals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {

        throw new NotFoundHttpException('Stránka již neexistuje!');
    }

}
