<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SlevyKategorie".
 *
 * @property integer $sleva_id
 * @property integer $kategorie_id
 * @property string $jmeno
 *
 * @property Slevy $sleva
 * @property Kategorie $kategorie
 */
class SlevyKategorie extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'SlevyKategorie';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sleva_id', 'kategorie_id', 'jmeno'], 'required'],
            [['sleva_id', 'kategorie_id'], 'integer'],
            [['jmeno'], 'string', 'max' => 160],
            [['sleva_id'], 'exist', 'skipOnError' => true, 'targetClass' => Slevy::className(), 'targetAttribute' => ['sleva_id' => 'DealId']],
            [['kategorie_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kategorie::className(), 'targetAttribute' => ['kategorie_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sleva_id' => 'Sleva ID',
            'kategorie_id' => 'Kategorie ID',
            'jmeno' => 'Jmeno',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSleva()
    {
        return $this->hasOne(Slevy::className(), ['DealId' => 'sleva_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKategorie()
    {
        return $this->hasOne(Kategorie::className(), ['id' => 'kategorie_id']);
    }
}
