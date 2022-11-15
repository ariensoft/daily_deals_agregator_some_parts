<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Papa".
 *
 * @property integer $Id
 * @property integer $DealId
 * @property string $Date
 *
 * @property Slevy $deal
 */
class Papa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Papa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DealId'], 'required'],
            [['DealId'], 'integer'],
            [['Date'], 'safe'],
            [['DealId'], 'exist', 'skipOnError' => true, 'targetClass' => Slevy::className(), 'targetAttribute' => ['DealId' => 'DealId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'DealId' => 'Deal ID',
            'Date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeal()
    {
        return $this->hasOne(Slevy::className(), ['DealId' => 'DealId']);
    }
}
