<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SlevyObrazky".
 *
 * @property integer $id
 * @property integer $DealId
 * @property string $Url
 * @property string $UrlLocal
 *
 * @property Slevy $deal
 */
class SlevyObrazky extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'SlevyObrazky';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DealId', 'Url', 'UrlLocal'], 'required'],
            [['DealId'], 'integer'],
            [['Url', 'UrlLocal'], 'string', 'max' => 255],
            [['DealId'], 'exist', 'skipOnError' => true, 'targetClass' => Slevy::className(), 'targetAttribute' => ['DealId' => 'DealId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'DealId' => 'Deal ID',
            'Url' => 'Url',
            'UrlLocal' => 'Url Local',
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
