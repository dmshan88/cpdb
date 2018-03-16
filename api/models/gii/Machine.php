<?php

namespace app\models\gii;

use Yii;

/**
 * This is the model class for table "machine".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $path
 * @property string $mac
 * @property int $ip
 * @property string $stat
 *
 * @property Cpdborder[] $cpdborders
 * @property Paneltest[] $paneltests
 */
class Machine extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'machine';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'stat'], 'string'],
            [['ip'], 'integer'],
            [['name'], 'string', 'max' => 10],
            [['path'], 'string', 'max' => 45],
            [['mac'], 'string', 'max' => 12],
            [['name'], 'unique'],
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
            'type' => 'Type',
            'path' => 'Path',
            'mac' => 'Mac',
            'ip' => 'Ip',
            'stat' => 'Stat',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpdborders()
    {
        return $this->hasMany(Cpdborder::className(), ['machine_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaneltests()
    {
        return $this->hasMany(Paneltest::className(), ['machine_id' => 'id']);
    }
}
