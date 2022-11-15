<?php

namespace app\controllers;

use Yii;
use app\models\Papa;
use app\models\Slevy;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PapaController implements the CRUD actions for Papa model.
 */
class PapaController extends Controller {

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
     * Lists all Papa models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PapaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Papa model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Papa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Papa();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Papa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Papa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Papa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Papa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Papa::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGo($id) {
        if (!empty($id)) {
            $dealData = Slevy::find()->with('server')->where('DealId=:id', array(':id' => $id))->one();
            //$dealData->Priority = $dealData->Priority;// + 1;
            if ($dealData) {
                $papa = new Papa;
                $papa->DealId = $id;
                $papa->save();
                $commisionPart = urldecode($dealData->server->UrlAdd);
                $urlPart = urldecode($dealData->Url);

                switch ($dealData->server->AddType) {
                    case 3:
                        //custom
                        $url = $dealData->Url;
                        break;

                    case 2:
                        //za
                        $url = $urlPart . $commisionPart;
                        break;

                    case 1:
                        //pred
                        $url = $commisionPart . $urlPart;
                        break;

                    default:
                        $url = $dealData->Url;
                }
                //$dealData->save();
                Yii::$app->response->redirect($url);
                //echo '<a href="'.$url.'">'.$url.'</a>';
            }else{
                throw new \yii\web\NotFoundHttpException('Stránka již neexistuje!');
            }
        }
    }

}
