<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model cms\models\Lo */

$this->title = 'Tạo phiếu xuất kho';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách phiếu xuất kho', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lo-create">
    <?= $this->render('_form', [
        'model' => $model,
        'error' => $error,
    ]) ?>

</div>

