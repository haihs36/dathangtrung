<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model cms\models\TbService */

$this->title = 'Thêm phí dịch vụ';
$this->params['breadcrumbs'][] = ['label' => 'Phí dịch vụ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-service-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
