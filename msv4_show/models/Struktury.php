<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Struktury".
 *
 * @property integer $id
 * @property integer $ServerId
 * @property string $Perex
 * @property string $Description
 * @property string $Points
 * @property string $Images
 * @property string $DescriptionSource
 * @property string $DescriptionTag
 * @property string $AddressSource
 * @property string $AddressTag
 *
 * @property Servers $server
 */
class Struktury extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Struktury';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ServerId', 'Perex', 'Description', 'Points', 'Images', 'DescriptionSource', 'DescriptionTag', 'AddressSource', 'AddressTag'], 'required'],
            [['ServerId'], 'integer'],
            [['Perex', 'Description', 'Points', 'Images', 'DescriptionTag', 'AddressTag'], 'string', 'max' => 100],
            [['DescriptionSource', 'AddressSource'], 'string', 'max' => 50],
            [['ServerId'], 'exist', 'skipOnError' => true, 'targetClass' => Servers::className(), 'targetAttribute' => ['ServerId' => 'ServerId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ServerId' => 'Server ID',
            'Perex' => 'Perex',
            'Description' => 'Description',
            'Points' => 'Points',
            'Images' => 'Images',
            'DescriptionSource' => 'Description Source',
            'DescriptionTag' => 'Description Tag',
            'AddressSource' => 'Address Source',
            'AddressTag' => 'Address Tag',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServer()
    {
        return $this->hasOne(Servers::className(), ['ServerId' => 'ServerId']);
    }
}
