<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Province */

$this->title = 'Chỉnh sửa: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách tỉnh thành', 'url' => ['index']];
?>
<div class="province-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
