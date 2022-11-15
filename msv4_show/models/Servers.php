<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Servers".
 *
 * @property integer $ServerId
 * @property string $Payments
 * @property string $Name
 * @property string $Logo
 * @property string $FeedUrl
 * @property string $FeedType
 * @property integer $Priority
 * @property string $UrlAdd
 * @property string $UrlAddBrno
 * @property integer $AddType
 * @property string $Element
 * @property integer $Insurance
 * @property integer $DisplayDescription
 *
 * @property Slevy[] $slevies
 * @property Struktury[] $strukturies
 */
class Servers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Servers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Payments', 'Name', 'Logo', 'FeedUrl', 'FeedType', 'Priority', 'UrlAdd', 'UrlAddBrno', 'AddType', 'Element', 'DisplayDescription'], 'required'],
            [['Priority', 'AddType', 'Insurance', 'DisplayDescription'], 'integer'],
            [['Payments'], 'string', 'max' => 500],
            [['Name', 'UrlAdd', 'Element'], 'string', 'max' => 300],
            [['Logo', 'UrlAddBrno'], 'string', 'max' => 255],
            [['FeedUrl'], 'string', 'max' => 160],
            [['FeedType'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ServerId' => 'Server ID',
            'Payments' => 'Payments',
            'Name' => 'Name',
            'Logo' => 'Logo',
            'FeedUrl' => 'Feed Url',
            'FeedType' => 'Feed Type',
            'Priority' => 'Priority',
            'UrlAdd' => 'Url Add',
            'UrlAddBrno' => 'Url Add Brno',
            'AddType' => 'Add Type',
            'Element' => 'Element',
            'Insurance' => 'Insurance',
            'DisplayDescription' => 'Display Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevies()
    {
        return $this->hasMany(Slevy::className(), ['ServerId' => 'ServerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStrukturies()
    {
        return $this->hasMany(Struktury::className(), ['ServerId' => 'ServerId']);
    }
}
