<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbOrdersDetail */

$this->title = 'Create Tb Orders Detail';
$this->params['breadcrumbs'][] = ['label' => 'Tb Orders Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-orders-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
