<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Change Target';
$this->params['breadcrumbs'][] = $this->title;

?>
<?= $msg ?>
<?= Html::beginForm('', 'post',['name'=>'changetarget']) ?>
<?php
    // var_dump($providearr);
    // var_dump($grouparr);

        $columns= [
            'name'
        ];
        foreach ($calinamearr as $caliid => $calirecord) {
            $columns[]=[
                'attribute' => strval($calirecord),
                'format' => 'html',
                'content' => 
                    function ($model,$key, $index, $column) use ($caliid){
                        return array_key_exists($caliid, $model)?Html::input('text','changetarget['.$key.']['.$caliid.']').$model[$caliid]:'/';

                    }
            ];
        }
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ]);
 ?>
  <?= Html::submitButton('Submit', ['class' => 'submit']) ?>
 <?= Html::endForm() ?>
