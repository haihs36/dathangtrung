<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model cms\modules\coupons\models\Chietkhau */

$this->title = 'Create Chietkhau';
$this->params['breadcrumbs'][] = ['label' => 'Chietkhaus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chietkhau-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
