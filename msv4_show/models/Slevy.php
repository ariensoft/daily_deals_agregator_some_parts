<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Slevy".
 *
 * @property integer $DealId
 * @property string $Hash
 * @property integer $ServerId
 * @property string $Text
 * @property string $TextFull
 * @property string $SearchText
 * @property string $FeedKws
 * @property integer $FPrice
 * @property integer $OPrice
 * @property integer $Discount
 * @property string $DStart
 * @property string $DEnd
 * @property string $Url
 * @property string $Image
 * @property string $OriginalImage
 * @property integer $Customers
 * @property integer $Status
 * @property double $Rating
 *
 * @property Papa[] $papas
 * @property Servers $server
 * @property SlevyKategorie[] $slevyKategories
 * @property Kategorie[] $kategories
 * @property SlevyMesta[] $slevyMestas
 * @property Cities[] $cities
 * @property SlevyMeta $slevyMeta
 * @property SlevyObrazky[] $slevyObrazkies
 * @property SlevyTagy[] $slevyTagies
 * @property Tagy[] $tags
 */
class Slevy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Slevy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Hash', 'ServerId', 'Text', 'TextFull', 'SearchText', 'FeedKws', 'FPrice', 'OPrice', 'Discount', 'DStart', 'DEnd', 'Url', 'Image', 'OriginalImage', 'Customers', 'Status'], 'required'],
            [['ServerId', 'FPrice', 'OPrice', 'Discount', 'Customers', 'Status'], 'integer'],
            [['TextFull'], 'string'],
            [['DStart', 'DEnd'], 'safe'],
            [['Rating'], 'number'],
            [['Hash'], 'string', 'max' => 300],
            [['Text', 'SearchText'], 'string', 'max' => 1000],
            [['FeedKws', 'Url', 'Image', 'OriginalImage'], 'string', 'max' => 500],
            [['ServerId'], 'exist', 'skipOnError' => true, 'targetClass' => Servers::className(), 'targetAttribute' => ['ServerId' => 'ServerId']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DealId' => 'Deal ID',
            'Hash' => 'Hash',
            'ServerId' => 'Server ID',
            'Text' => 'Text',
            'TextFull' => 'Text Full',
            'SearchText' => 'Search Text',
            'FeedKws' => 'Feed Kws',
            'FPrice' => 'Fprice',
            'OPrice' => 'Oprice',
            'Discount' => 'Discount',
            'DStart' => 'Dstart',
            'DEnd' => 'Dend',
            'Url' => 'Url',
            'Image' => 'Image',
            'OriginalImage' => 'Original Image',
            'Customers' => 'Customers',
            'Status' => 'Status',
            'Rating' => 'Rating',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPapas()
    {
        return $this->hasMany(Papa::className(), ['DealId' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServer()
    {
        return $this->hasOne(Servers::className(), ['ServerId' => 'ServerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevyCategories()
    {
        return $this->hasMany(SlevyKategorie::className(), ['sleva_id' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Kategorie::className(), ['id' => 'kategorie_id'])->viaTable('SlevyKategorie', ['sleva_id' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevyMestas()
    {
        return $this->hasMany(SlevyMesta::className(), ['DealId' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(Cities::className(), ['CityId' => 'CityId'])->viaTable('SlevyMesta', ['DealId' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeta()
    {
        return $this->hasOne(SlevyMeta::className(), ['DealId' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(SlevyObrazky::className(), ['DealId' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevyTagies()
    {
        return $this->hasMany(SlevyTagy::className(), ['sleva_id' => 'DealId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tagy::className(), ['tag_id' => 'tag_id'])->viaTable('SlevyTagy', ['sleva_id' => 'DealId']);
    }
}
