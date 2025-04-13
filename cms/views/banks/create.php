<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbBank */

$this->title = 'Create Tb Bank';
$this->params['breadcrumbs'][] = ['label' => 'Tb Banks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-bank-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
