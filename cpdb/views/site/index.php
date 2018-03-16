<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cpdborders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cpdborder-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= 
            Html::a('Change Target', ['changetarget'], ['class' => 'btn btn-success']) 
        ?>
    </p>
    <p> <?= $msg ?>  </p>
    <?= Html::beginForm('', 'post',['name'=>'neworder']) ?>
    <?= Html::label('ordername:', 'ordername') ?>
    <?= Html::textInput('ordername') ?>
    <?= Html::label('machine:', 'machine') ?>
    <?= Html::dropDownList('machine', 1,$machinearr) ?>
    <?= Html::label('panel:', 'panel') ?>
    <?= Html::dropDownList('panel', 1,$panelarr) ?>
    <?= Html::label('panellot:', 'panellot') ?>
    <?= Html::textInput('panellot') ?>
    <?= Html::submitButton('New Order', ['class' => 'submit']) ?>
    <?= Html::endForm() ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [

            // 'id',
            'startdate',
            'ordername',
            'machinename',
            'panelname',
            'panellot',
            //'finishdate',
            'stat',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => ' {showtest}{download}',
                'buttons' => [
                        'showtest'=>function ($url, $model, $key) {

                            return $model['stat'] === 'ON' ? Html::a('show', $url) : '';
                            },
                        'download'=>function ($url, $model, $key) {
                            return $model['stat'] === 'OFF' ? Html::a('download', $url) : '';
                            },
                        ]
            ],
        ],
    ]); ?>
</div>
