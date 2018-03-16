<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property string $name
 * @property int $calithree
 * @property string $unit
 * @property int $kround
 * @property int $bround
 *
 * @property Cpdbcalc[] $cpdbcalcs
 * @property Cpdbgroup[] $cpdbgroups
 * @property Cpdbkb[] $cpdbkbs
 * @property Cpdborder[] $cpdborders
 * @property Itemcali[] $itemcalis
 * @property Calibrator[] $calibrators
 * @property Panelitem[] $panelitems
 * @property Panel[] $panels
 * @property Panelresult[] $panelresults
 * @property Paneltest[] $paneltests
 */
class Item extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'calithree'], 'required'],
            [['id', 'calithree', 'kround', 'bround'], 'integer'],
            [['name'], 'string', 'max' => 15],
            [['unit'], 'string', 'max' => 10],
            [['name'], 'unique'],
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
            'calithree' => 'Calithree',
            'unit' => 'Unit',
            'kround' => 'Kround',
            'bround' => 'Bround',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbcalcs()
    {
        return $this->hasMany(Cpdbcalc::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbgroups()
    {
        return $this->hasMany(Cpdbgroup::className(), ['id' => 'cpdbgroup_id'])->viaTable('cpdbcalc', ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbkbs()
    {
        return $this->hasMany(Cpdbkb::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdborders()
    {
        return $this->hasMany(Cpdborder::className(), ['id' => 'cpdborder_id'])->viaTable('cpdbkb', ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemcalis()
    {
        return $this->hasMany(Itemcali::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalibrators()
    {
        return $this->hasMany(Calibrator::className(), ['id' => 'calibrator_id'])->viaTable('itemcali', ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelitems()
    {
        return $this->hasMany(Panelitem::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanels()
    {
        return $this->hasMany(Panel::className(), ['id' => 'panel_id'])->viaTable('panelitem', ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPanelresults()
    {
        return $this->hasMany(Panelresult::className(), ['item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaneltests()
    {
        return $this->hasMany(Paneltest::className(), ['id' => 'paneltest_id'])->viaTable('panelresult', ['item_id' => 'id']);
    }
}
