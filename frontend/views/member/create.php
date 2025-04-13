<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\TbCustomers */

$this->title = 'Create Tb Member';
$this->params['breadcrumbs'][] = ['label' => 'Tb Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-member-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
