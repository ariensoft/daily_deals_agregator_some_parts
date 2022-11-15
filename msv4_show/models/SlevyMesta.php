<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SlevyMesta".
 *
 * @property integer $DealId
 * @property integer $CityId
 * @property string $Name
 *
 * @property Slevy $deal
 * @property Cities $city
 */
class SlevyMesta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'SlevyMesta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DealId', 'CityId', 'Name'], 'required'],
            [['DealId', 'CityId'], 'integer'],
            [['Name'], 'string', 'max' => 160],
            [['DealId'], 'exist', 'skipOnError' => true, 'targetClass' => Slevy::className(), 'targetAttribute' => ['DealId' => 'DealId']],
            [['CityId'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['CityId' => 'CityId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DealId' => 'Deal ID',
            'CityId' => 'City ID',
            'Name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeal()
    {
        return $this->hasOne(Slevy::className(), ['DealId' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['CityId' => 'CityId']);
    }
}
