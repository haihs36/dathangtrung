<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Cny */

$this->title = 'Tạo tỷ giá';
$this->params['breadcrumbs'][] = ['label' => 'Quản lý tỷ giá', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cny-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
