<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\models\TbMenu */

$this->title = Yii::t('cms', 'Update {modelClass}: ', [
    'modelClass' => 'Tb Menu',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('cms', 'Menus'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('cms', 'Update');
?>
<div class="tb-menu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
