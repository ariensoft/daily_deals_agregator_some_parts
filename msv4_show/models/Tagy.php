<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Tagy".
 *
 * @property integer $tag_id
 * @property string $tag_jmeno
 * @property string $tag_kws
 * @property integer $tag_display
 *
 * @property SlevyTagy[] $slevyTagies
 * @property Slevy[] $slevas
 */
class Tagy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Tagy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag_jmeno', 'tag_kws', 'tag_display'], 'required'],
            [['tag_display'], 'integer'],
            [['tag_jmeno'], 'string', 'max' => 100],
            [['tag_kws'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'tag_jmeno' => 'Tag Jmeno',
            'tag_kws' => 'Tag Kws',
            'tag_display' => 'Tag Display',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevyTagies()
    {
        return $this->hasMany(SlevyTagy::className(), ['tag_id' => 'tag_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevas()
    {
        return $this->hasMany(Slevy::className(), ['DealId' => 'sleva_id'])->viaTable('SlevyTagy', ['tag_id' => 'tag_id']);
    }
}
