<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "cpdbgroup".
 *
 * @property int $id
 * @property string $stat
 * @property string $inistat
 * @property string $recheck
 * @property int $cpdborder_id
 * @property int $calibrator_id
 * @property string $calibratorlot
 *
 * @property Cpdbcalc[] $cpdbcalcs
 * @property Item[] $items
 * @property Calibrator $calibrator
 * @property Cpdborder $cpdborder
 */
class Cpdbgroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cpdbgroup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stat', 'inistat', 'recheck'], 'string'],
            [['cpdborder_id', 'calibrator_id'], 'required'],
            [['cpdborder_id', 'calibrator_id'], 'integer'],
            [['calibratorlot'], 'string', 'max' => 40],
            [['calibrator_id'], 'exist', 'skipOnError' => true, 'targetClass' => Calibrator::className(), 'targetAttribute' => ['calibrator_id' => 'id']],
            [['cpdborder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cpdborder::className(), 'targetAttribute' => ['cpdborder_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stat' => 'Stat',
            'inistat' => 'Inistat',
            'recheck' => 'Recheck',
            'cpdborder_id' => 'Cpdborder ID',
            'calibrator_id' => 'Calibrator ID',
            'calibratorlot' => 'Calibratorlot',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbcalcs()
    {
        return $this->hasMany(Cpdbcalc::className(), ['cpdbgroup_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('cpdbcalc', ['cpdbgroup_id' => 'id']);
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
    public function getCpdborder()
    {
        return $this->hasOne(Cpdborder::className(), ['id' => 'cpdborder_id']);
    }
    ////////////////////////////
    public function getPaneltests()
    {
        return $this->hasMany(Paneltest::className(), ['groupid' => 'id']);
    }
}
