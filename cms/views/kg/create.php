<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TbKg */

$this->title = 'Tạo mới giá cước cân nặng';
$this->params['breadcrumbs'][] = ['label' => 'Giá cước cân nặng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-kg-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
