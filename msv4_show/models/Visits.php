<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Visits".
 *
 * @property integer $id
 * @property integer $visitor_id
 * @property string $page
 * @property string $date
 * @property string $referer
 * @property string $ip
 * @property string $host
 * @property string $agent
 */
class Visits extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Visits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['visitor_id'], 'required'],
            [['visitor_id'], 'string', 'max' => 100],
            [['date'], 'safe'],
            [['page', 'referer', 'agent', 'host'], 'string', 'max' => 255],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'visitor_id' => 'Visitor ID',
            'page' => 'Page',
            'date' => 'Date',
            'referer' => 'Referer',
            'ip' => 'Ip',
            'host' => 'Host',
            'agent' => 'Agent',
        ];
    }
    
            public function getVisitor()
    {
        return $this->hasOne(Visitors::className(), ['id' => 'visitor_id']);
    }
}
