<?php

use yii\helpers\Html;
use yii\grid\GridView;


$this->title = 'Cài đặt';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-settings-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Tạo mới', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
     <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
         //   ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'name_public:ntext',
            [
                'label'      => 'value',
                'headerOptions'  => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getContent();
                }
            ],
            'type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
