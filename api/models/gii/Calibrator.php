<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "calibrator".
 *
 * @property int $id
 * @property string $name
 *
 * @property Cpdbgroup[] $cpdbgroups
 * @property Itemcali[] $itemcalis
 * @property Item[] $items
 */
class Calibrator extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calibrator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 15],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbgroups()
    {
        return $this->hasMany(Cpdbgroup::className(), ['calibrator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemcalis()
    {
        return $this->hasMany(Itemcali::className(), ['calibrator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('itemcali', ['calibrator_id' => 'id']);
    }
}
