<?php

use yii\helpers\Html;



$this->title = 'Tạo mới giá ';
$this->params['breadcrumbs'][] = ['label' => 'Giá phí vận chuyển', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-kg-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
