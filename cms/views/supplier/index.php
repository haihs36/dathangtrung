<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbSupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tb Suppliers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-supplier-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title',
            'slug',
            'address',
            'email:email',
            // 'phone',
            // 'fax',
            // 'status',
            // 'create_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
