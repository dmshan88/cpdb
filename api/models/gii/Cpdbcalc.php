<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "cpdbcalc".
 *
 * @property int $cpdbgroup_id
 * @property int $item_id
 * @property double $target
 * @property double $xvalue
 * @property double $xsum
 * @property int $xcount
 *
 * @property Cpdbgroup $cpdbgroup
 * @property Item $item
 */
class Cpdbcalc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cpdbcalc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cpdbgroup_id', 'item_id'], 'required'],
            [['cpdbgroup_id', 'item_id', 'xcount'], 'integer'],
            [['target', 'xvalue', 'xsum'], 'number'],
            [['cpdbgroup_id', 'item_id'], 'unique', 'targetAttribute' => ['cpdbgroup_id', 'item_id']],
            [['cpdbgroup_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cpdbgroup::className(), 'targetAttribute' => ['cpdbgroup_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cpdbgroup_id' => 'Cpdbgroup ID',
            'item_id' => 'Item ID',
            'target' => 'Target',
            'xvalue' => 'Xvalue',
            'xsum' => 'Xsum',
            'xcount' => 'Xcount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbgroup()
    {
        return $this->hasOne(Cpdbgroup::className(), ['id' => 'cpdbgroup_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
}
