<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Cities".
 *
 * @property integer $CityId
 * @property string $Name
 * @property string $Region
 * @property string $Province
 * @property integer $Residents
 * @property double $Lat
 * @property double $Lng
 * @property integer $Approved
 *
 * @property SlevyMesta[] $slevyMestas
 * @property Slevy[] $deals
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Cities';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Residents', 'Approved'], 'integer'],
            [['Lat', 'Lng'], 'number'],
            [['Name'], 'string', 'max' => 42],
            [['Region'], 'string', 'max' => 21],
            [['Province'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CityId' => 'City ID',
            'Name' => 'Name',
            'Region' => 'Region',
            'Province' => 'Province',
            'Residents' => 'Residents',
            'Lat' => 'Lat',
            'Lng' => 'Lng',
            'Approved' => 'Approved',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevyMestas()
    {
        return $this->hasMany(SlevyMesta::className(), ['CityId' => 'CityId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeals()
    {
        return $this->hasMany(Slevy::className(), ['DealId' => 'DealId'])->viaTable('SlevyMesta', ['CityId' => 'CityId']);
    }
}
