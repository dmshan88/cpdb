<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "paneltest".
 *
 * @property int $id
 * @property string $chkdatetime
 * @property int $machine_id
 * @property int $panel_id
 * @property string $type
 * @property int $groupid
 *
 * @property Panelresult[] $panelresults
 * @property Item[] $items
 * @property Machine $machine
 * @property Panel $panel
 */
class Paneltest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paneltest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chkdatetime', 'machine_id', 'panel_id', 'type'], 'required'],
            [['chkdatetime'], 'safe'],
            [['machine_id', 'panel_id', 'groupid'], 'integer'],
            [['type'], 'string'],
            [['machine_id', 'chkdatetime'], 'unique', 'targetAttribute' => ['machine_id', 'chkdatetime']],
            [['machine_id'], 'exist', 'skipOnError' => true, 'targetClass' => Machine::className(), 'targetAttribute' => ['machine_id' => 'id']],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Panel::className(), 'targetAttribute' => ['panel_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chkdatetime' => 'Chkdatetime',
            'machine_id' => 'Machine ID',
            'panel_id' => 'Panel ID',
            'type' => 'Type',
            'groupid' => 'Groupid',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelresults()
    {
        return $this->hasMany(Panelresult::className(), ['paneltest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('panelresult', ['paneltest_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMachine()
    {
        return $this->hasOne(Machine::className(), ['id' => 'machine_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanel()
    {
        return $this->hasOne(Panel::className(), ['id' => 'panel_id']);
    }

    /////////////////////////////
    public function getCpdbgroup()
    {
        return $this->hasOne(Cpdbgroup::className(), ['id' => 'groupid']);
    }
}
