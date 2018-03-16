<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "panelitem".
 *
 * @property int $panel_id
 * @property int $item_id
 * @property int $position
 * @property int $hole
 * @property string $hastwo
 *
 * @property Item $item
 * @property Panel $panel
 */
class Panelitem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'panelitem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['panel_id', 'item_id', 'position', 'hole'], 'required'],
            [['panel_id', 'item_id', 'position', 'hole'], 'integer'],
            [['hastwo'], 'string'],
            [['panel_id', 'item_id'], 'unique', 'targetAttribute' => ['panel_id', 'item_id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Panel::className(), 'targetAttribute' => ['panel_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'panel_id' => 'Panel ID',
            'item_id' => 'Item ID',
            'position' => 'Position',
            'hole' => 'Hole',
            'hastwo' => 'Hastwo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanel()
    {
        return $this->hasOne(Panel::className(), ['id' => 'panel_id']);
    }
}
