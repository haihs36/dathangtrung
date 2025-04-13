<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\TbSettings */

$this->title =  'Update '  . $model->name;


$this->params['breadcrumbs'][] = ['label' => 'Cài đặt', 'url' => ['list']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tb-settings-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
