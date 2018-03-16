<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "itemcali".
 *
 * @property int $item_id
 * @property int $calibrator_id
 * @property double $target
 *
 * @property Calibrator $calibrator
 * @property Item $item
 */
class Itemcali extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'itemcali';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'calibrator_id'], 'required'],
            [['item_id', 'calibrator_id'], 'integer'],
            [['target'], 'number'],
            [['item_id', 'calibrator_id'], 'unique', 'targetAttribute' => ['item_id', 'calibrator_id']],
            [['calibrator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Calibrator::className(), 'targetAttribute' => ['calibrator_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id' => 'Item ID',
            'calibrator_id' => 'Calibrator ID',
            'target' => 'Target',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalibrator()
    {
        return $this->hasOne(Calibrator::className(), ['id' => 'calibrator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::className(), ['id' => 'item_id']);
    }
}
