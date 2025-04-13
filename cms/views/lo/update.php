<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\Lo */
/* @var $dataRender cms\models\Lo */
$this->title = 'Thông tin phiếu xuất: PXK-' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Danh sách phiếu xuất kho', 'url' => ['index']];

?>
<div class="lo-update">
    <?= $this->render('_form', [
        'model' => $model,
        'dataRender' => $dataRender,
        'error' => $error,
    ]) ?>

</div>
