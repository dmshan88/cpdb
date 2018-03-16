<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "cpdborder".
 *
 * @property int $id
 * @property string $ordername
 * @property int $machine_id
 * @property int $panel_id
 * @property string $panellot
 * @property string $startdate
 * @property string $finishdate
 * @property string $stat
 *
 * @property Cpdbgroup[] $cpdbgroups
 * @property Cpdbkb[] $cpdbkbs
 * @property Item[] $items
 * @property Machine $machine
 * @property Panel $panel
 */
class Cpdborder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cpdborder';
    }

    /**
     * @inheritdoc
     */
/*    public function attributes(){
        $array = $this->attributes();
        $array[] = 'showname';
        return $array;
    }*/
    public function rules()
    {
        return [
            [['ordername', 'machine_id', 'panel_id', 'startdate'], 'required'],
            [['machine_id', 'panel_id'], 'integer'],
            [['startdate', 'finishdate'], 'safe'],
            [['stat'], 'string'],
            [['ordername'], 'string', 'max' => 20],
            [['panellot'], 'string', 'max' => 10],
            [['ordername'], 'unique'],
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
            'ordername' => 'Ordername',
            'machine_id' => 'Machine ID',
            'panel_id' => 'Panel ID',
            'panellot' => 'Panellot',
            'startdate' => 'Startdate',
            'finishdate' => 'Finishdate',
            'stat' => 'Stat',
            'showname' => 'Stat',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbgroups()
    {
        return $this->hasMany(Cpdbgroup::className(), ['cpdborder_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdbkbs()
    {
        return $this->hasMany(Cpdbkb::className(), ['cpdborder_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['id' => 'item_id'])->viaTable('cpdbkb', ['cpdborder_id' => 'id']);
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
}
