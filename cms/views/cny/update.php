<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Cny */

$this->title = 'Chỉnh sửa tỷ giá ';
$this->params['breadcrumbs'][] = ['label' => 'Quản lý tỷ giá', 'url' => ['index']];
?>
<div class="cny-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
