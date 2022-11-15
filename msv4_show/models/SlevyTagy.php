<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SlevyTagy".
 *
 * @property integer $sleva_id
 * @property integer $tag_id
 * @property string $tag_jmeno
 *
 * @property Slevy $sleva
 * @property Tagy $tag
 */
class SlevyTagy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'SlevyTagy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sleva_id', 'tag_id', 'tag_jmeno'], 'required'],
            [['sleva_id', 'tag_id'], 'integer'],
            [['tag_jmeno'], 'string', 'max' => 100],
            [['sleva_id'], 'exist', 'skipOnError' => true, 'targetClass' => Slevy::className(), 'targetAttribute' => ['sleva_id' => 'DealId']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tagy::className(), 'targetAttribute' => ['tag_id' => 'tag_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sleva_id' => 'Sleva ID',
            'tag_id' => 'Tag ID',
            'tag_jmeno' => 'Tag Jmeno',
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
    public function getTag()
    {
        return $this->hasOne(Tagy::className(), ['tag_id' => 'tag_id']);
    }
}
