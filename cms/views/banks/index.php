<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbBankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Danh sách các ngân hàng';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-bank-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Thêm ngân hàng', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'stk',
            'bankName',
            'bankAcount',
            'branch',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
