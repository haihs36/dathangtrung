<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model cms\models\TbOrderComplain */

$this->title = 'Create Tb Order Complain';
$this->params['breadcrumbs'][] = ['label' => 'Tb Order Complains', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-order-complain-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
