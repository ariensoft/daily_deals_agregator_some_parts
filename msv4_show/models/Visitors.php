<?php

namespace app\models;

use Yii;
use yii\web\Cookie;

/**
 * This is the model class for table "Visitors".
 *
 * @property integer $id
 * @property string $cookie
 * @property string $created
 * @property string $mail
 * @property string $agent
 */
class Visitors extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'Visitors';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cookie'], 'required'],
            [['created'], 'safe'],
            [['cookie'], 'string', 'max' => 100],
            [['mail', 'agent'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'cookie' => 'Cookie',
            'created' => 'Created',
            'mail' => 'Mail',
        ];
    }

    public function getVisits() {
        return $this->hasMany(Visits::className(), ['visitor_id' => 'id']);
    }

    public function startSpy() {

        $userUId = \Yii::$app->getRequest()->getCookies()->getValue('pepa');
        $userHost = Yii::$app->request->userHost;
        $userIP = Yii::$app->request->userIP;
        $userAgent = Yii::$app->request->userAgent;
        $userIsBot = Visitors::bot_detected($userAgent . ' ' . $userHost);
        $page = Yii::$app->request->absoluteUrl;
        $referer = Yii::$app->request->referrer;

        if (!empty($userUId)) {

            $userUId = (string) $userUId;
        } else {

            if ($userIsBot == FALSE) {

                $userUId = uniqid('ms_', true);

                $user_cookie = new Cookie([
                    'name' => 'pepa',
                    'value' => $userUId,
                    'expire' => time() + 86400 * 365,
                    'httpOnly' => true,
                ]);
                \Yii::$app->getResponse()->getCookies()->add($user_cookie);
                //$userUId = \Yii::$app->getRequest()->getCookies()->getValue('pepa');

                $newVisitor = new Visitors();
                $newVisitor->cookie = (string) $userUId;
                $newVisitor->agent = (string) $userAgent;
                $newVisitor->save();
            } else {

                $userUId = 'bot_' . uniqid('ms_', true);
                $bot_cookie = new Cookie([
                    'name' => 'pepa',
                    'value' => $userUId,
                    'expire' => time() + 86400 * 365,
                    'httpOnly' => true,
                ]);
                \Yii::$app->getResponse()->getCookies()->add($bot_cookie);
            }
        }

        if (!empty($userUId) && $userIsBot == FALSE) {
            
            $newVisit = new Visits();
            $newVisit->visitor_id = (string) $userUId;
            $newVisit->page = (string) $page;
            $newVisit->referer = (string) $referer;
            $newVisit->ip = (string) $userIP;
            $newVisit->host = (string) $userHost;
            $newVisit->agent = (string) $userAgent;
            $newVisit->save();
        }
    }

    public function bot_detected($userAgent) {

        if (!empty($userAgent) && preg_match('/bot|crawl|slurp|spider|screenshot|java|python/i', (string) $userAgent)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
