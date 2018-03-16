<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "panel".
 *
 * @property int $id
 * @property string $name
 * @property string $showname
 * @property string $type
 *
 * @property Cpdborder[] $cpdborders
 * @property Panelitem[] $panelitems
 * @property Item[] $items
 * @property Paneltest[] $paneltests
 */
class Panel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'panel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'showname', 'type'], 'required'],
            [['id'], 'integer'],
            [['type'], 'string'],
            [['name', 'showname'], 'string', 'max' => 45],
            [['id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'showname' => 'Showname',
            'type' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdborders()
    {
        return $this->hasMany(Cpdborder::className(), ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelitems()
    {
        return $this->hasMany(Panelitem::className(), ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('panelitem', ['panel_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaneltests()
    {
        return $this->hasMany(Paneltest::className(), ['panel_id' => 'id']);
    }
}
