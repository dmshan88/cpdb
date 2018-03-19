<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\grid\GridView;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Paneltests';
// $this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = [
    'label'=>'order-'.$orderid,
    'url'=>Url::to(['site/showtest','id'=>$orderid])
];
?>
<div class="paneltest-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <div>
        <?= $readmsg ?>
        <?= $msg ?>
    </div>
    <p>
        <?= 
            Html::a('Report', ['showreport', 'id' => $orderid], ['class' => 'btn btn-success']) 
        ?>
    </p>
    <?= Html::beginForm('', 'post',['name'=>'newgroup']) ?>
    <?= Html::dropDownList('caliid', 1,$caliarr) ?>
    <?= Html::input('text', 'calilot') ?>
    <?= Html::hiddenInput('orderid', $orderid) ?>
    <?= Html::submitButton('New Group', ['class' => 'submit']) ?>
    <?= Html::endForm() ?>

    <?= Html::beginForm('', 'post',['name'=>'cpdbkb']) ?>
    <?= Html::hiddenInput('orderid', $orderid) ?>
    <?php
        echo GridView::widget([
            'dataProvider' => $dataProvider1,
            'columns' => [
                'name',
                [
                    'attribute' => 'lot',
                    'format' => 'html',
                    'content' => function ($model,$key, $index, $column) {
                        if(empty($model['lot'])){
                            return Html::input('text','cpdbkb['.$model['item_id'].'][lot]',$model['lot']);                          
                        }
                        else{
                            return $model['lot'];
                        }
                    }
                ],
                [
                    'attribute' => 'K',
                    'format' => 'html',
                    'content' => function ($model,$key, $index, $column) {
                        if(empty($model['lot'])){
                            return Html::input('text','cpdbkb['.$model['item_id'].'][k]',$model['kvalue0']);
                        }
                        else{
                            return $model['kvalue0'];
                        }
                    }
                ],
                [
                    'attribute' => 'B',
                    'format' => 'html',
                    'content' => function ($model,$key, $index, $column) {
                        if(empty($model['lot'])){
                            return Html::input('text','cpdbkb['.$model['item_id'].'][b]',$model['bvalue0']);                        
                        }
                        else{
                            return $model['bvalue0'];
                        }
                    }
                ],
                'kvalue1',
                'bvalue1',
                'rvalue1',
                'kstr',
                'bstr',
            ],
        ]);
     ?>
      <?= Html::submitButton('Submit', ['class' => 'submit']) ?>
     <?= Html::endForm() ?>

    <?= Html::beginForm('', 'post',['name'=>'addgroup']) ?>
    <?= Html::hiddenInput('orderid', $orderid) ?>
    <?php
    // var_dump($providearr);
    // var_dump($grouparr);
        $grouplotarr[0]=' ';
        ksort($grouplotarr);
        $columns= [
            'chkdatetime',
            [
                'attribute' => 'calibrator',
                'format' => 'html',
                'content' => 
                    function ($model,$key, $index, $column) 
                        use($grouplotarr){
                        // var_dump($grouplotarr);
                    if(empty($model['groupid'])){
                        return Html::dropDownList('group['.$key.']', 0, $grouplotarr);
                    }
                    else{
                        // var_dump($model['groupid']);
                        // var_dump($grouplotarr);
                        if(array_key_exists($model['groupid'], $grouplotarr)){
                            return $grouplotarr[$model['groupid']];
                        }
                        else{
                            return 'error';
                        }
                    }
                }
            ],          
        ];
        foreach ($itemarr as $key1 => $value) {
            $columns[]=[
                'attribute' => $value,
                'format' => 'html',
                'content' => function ($model,$key, $index, $column) use($key1){
                    $abandon=$model['results'][$key1]['abandon'];
                    $result=$model['results'][$key1]['result'];
                    // return $result;
                     if($abandon=='Y'){
                        // return $result;
                        $options = ['style' => ['text-decoration' => 'line-through']];
                        return Html::tag('div', $result, $options);
                    }
                    else{
                        // return $model['groupstat'];
                        return $result;
                        if($model['groupstat']=='ON'){
                           return Html::checkbox('abandon['.$key.']['.$key1.']', $abandon=='Y', ['label' => $result]);
                        }
                        else{
                            return $result;
                        }
                    } 
                },
            ];
        }
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $columns,
    ]);
     ?>
     <?= Html::submitButton('Submit', ['class' => 'submit']) ?>
    <?= Html::a('Reset', ['site/resetresult', 'orderid' => $orderid]) ?>
     <?= Html::endForm() ?>
</div>
