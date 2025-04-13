<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TbKg */

$this->title = 'Sửa giá cước: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Giá cước', 'url' => ['index']];
/*$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];*/
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-kg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
