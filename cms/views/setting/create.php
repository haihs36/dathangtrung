<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model cms\models\TbSettings */

$this->title = 'Tạo mới';
$this->params['breadcrumbs'][] = ['label' => 'Cài đặt', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-settings-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
