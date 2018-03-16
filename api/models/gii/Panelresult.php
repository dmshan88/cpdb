<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "panelresult".
 *
 * @property int $paneltest_id
 * @property int $item_id
 * @property double $result
 * @property string $abnormal
 * @property string $abandon
 *
 * @property Item $item
 * @property Paneltest $paneltest
 */
class Panelresult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'panelresult';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paneltest_id', 'item_id'], 'required'],
            [['paneltest_id', 'item_id'], 'integer'],
            [['result'], 'number'],
            [['abnormal', 'abandon'], 'string'],
            [['paneltest_id', 'item_id'], 'unique', 'targetAttribute' => ['paneltest_id', 'item_id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::className(), 'targetAttribute' => ['item_id' => 'id']],
            [['paneltest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Paneltest::className(), 'targetAttribute' => ['paneltest_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'paneltest_id' => 'Paneltest ID',
            'item_id' => 'Item ID',
            'result' => 'Result',
            'abnormal' => 'Abnormal',
            'abandon' => 'Abandon',
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
    public function getPaneltest()
    {
        return $this->hasOne(Paneltest::className(), ['id' => 'paneltest_id']);
    }
}
