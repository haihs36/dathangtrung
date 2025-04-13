<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\OrdersFast */

$this->title = 'Chi tiết link';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách link', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orders-fast-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="pull-right">
        <?= Html::a('Danh sách link', ['index'], ['class' => 'btn btn-primary']) ?>

    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'link',
            'mobile',
            'note',
            'create_time',
            'fullname',
        ],
    ]) ?>

</div>
