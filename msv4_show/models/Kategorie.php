<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Kategorie".
 *
 * @property integer $id
 * @property integer $rodic
 * @property string $jmeno
 * @property string $kw
 * @property string $feed_kw
 * @property string $url
 * @property string $obrazek
 * @property integer $priorita
 *
 * @property SlevyKategorie[] $slevyKategories
 * @property Slevy[] $slevas
 */
class Kategorie extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Kategorie';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rodic', 'jmeno', 'kw', 'feed_kw', 'url', 'obrazek'], 'required'],
            [['rodic', 'priorita'], 'integer'],
            [['jmeno', 'url'], 'string', 'max' => 160],
            [['kw'], 'string', 'max' => 2000],
            [['obrazek', 'feed_kw'], 'string', 'max' => 255],
            [['url'], 'unique'],
            [['jmeno'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rodic' => 'Rodic',
            'jmeno' => 'Jmeno',
            'kw' => 'Kw',
            'feed_kw' => 'Feed Kw',
            'url' => 'Url',
            'obrazek' => 'Obrazek',
            'priorita' => 'Priorita',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevyKategories()
    {
        return $this->hasMany(SlevyKategorie::className(), ['kategorie_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlevas()
    {
        return $this->hasMany(Slevy::className(), ['DealId' => 'sleva_id'])->viaTable('SlevyKategorie', ['kategorie_id' => 'id']);
    }
    
        public function buildTree($items) {

        $childs = [];

        foreach ($items as &$item)
            $childs[$item['parent']][] = &$item;
        unset($item);

        foreach ($items as &$item)
            if (isset($childs[$item['id']]))
                $item['childs'] = $childs[$item['id']];

        return $childs[0];
    }

    public function find_parrents($id, $categories) {
        $path[] = $id;
        $parrent = $categories[$id]['parent'];
        $iter = 0;

        while ($parrent > 0) {
            $path[] = $parrent;
            $parrent = $categories[$parrent]['parent'];

            if ($iter >= 10) {
                break;
            }
            $iter++;
        }
        krsort($path);
        return($path);
    }

    public function tree_to_arr($tree, $id, &$results = array()) {
        
        foreach ($tree as $leaf) {
            $results[$leaf['id']] = ['id' => $leaf['id'] , 'jmeno' => $leaf['name']];
            if(isset($leaf['childs'])){
                Kategorie::tree_to_arr($leaf['childs'], $leaf['childs'][0]['id'], $results);
            }
        }
        return count($results) > 0 ? $results : FALSE;
    }
}
