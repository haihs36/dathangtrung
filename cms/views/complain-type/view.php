<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbComplainType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Tb Complain Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-complain-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'create_date',
        ],
    ]) ?>

</div>
