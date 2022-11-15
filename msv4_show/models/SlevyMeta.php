<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SlevyMeta".
 *
 * @property integer $Id
 * @property integer $DealId
 * @property string $Discovered
 * @property double $Sph
 * @property double $PrevSph
 * @property integer $Ssf
 * @property integer $StartingSales
 * @property string $Perex
 * @property string $DescriptionFull
 * @property string $Address
 *
 * @property Slevy $deal
 */
class SlevyMeta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'SlevyMeta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['DealId', 'Discovered', 'Sph', 'PrevSph', 'Ssf', 'StartingSales', 'Perex', 'DescriptionFull', 'Address'], 'required'],
            [['DealId', 'Ssf', 'StartingSales'], 'integer'],
            [['Discovered'], 'safe'],
            [['Sph', 'PrevSph'], 'number'],
            [['DescriptionFull'], 'string'],
            [['Perex', 'Address'], 'string', 'max' => 500],
            [['DealId'], 'unique'],
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
            'Discovered' => 'Discovered',
            'Sph' => 'Sph',
            'PrevSph' => 'Prev Sph',
            'Ssf' => 'Ssf',
            'StartingSales' => 'Starting Sales',
            'Perex' => 'Perex',
            'DescriptionFull' => 'Description Full',
            'Address' => 'Address',
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
