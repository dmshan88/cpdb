<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "cpdbkb".
 *
 * @property int $cpdborder_id
 * @property int $item_id
 * @property string $lot
 * @property double $kvalue0
 * @property double $bvalue0
 * @property double $kvalue1
 * @property double $bvalue1
 * @property double $rvalue1
 * @property string $kstr
 * @property string $bstr
 *
 * @property Cpdborder $cpdborder
 * @property Item $item
 */
class Cpdbkb extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cpdbkb';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cpdborder_id', 'item_id'], 'required'],
            [['cpdborder_id', 'item_id'], 'integer'],
            [['kvalue0', 'bvalue0', 'kvalue1', 'bvalue1', 'rvalue1'], 'number'],
            [['lot', 'kstr', 'bstr'], 'string', 'max' => 10],
            [['cpdborder_id', 'item_id'], 'unique', 'targetAttribute' => ['cpdborder_id', 'item_id']],
            [['cpdborder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cpdborder::className(), 'targetAttribute' => ['cpdborder_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cpdborder_id' => 'Cpdborder ID',
            'item_id' => 'Item ID',
            'lot' => 'Lot',
            'kvalue0' => 'Kvalue0',
            'bvalue0' => 'Bvalue0',
            'kvalue1' => 'Kvalue1',
            'bvalue1' => 'Bvalue1',
            'rvalue1' => 'Rvalue1',
            'kstr' => 'Kstr',
            'bstr' => 'Bstr',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdborder()
    {
        return $this->hasOne(Cpdborder::className(), ['id' => 'cpdborder_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
}
