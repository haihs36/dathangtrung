<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbSupplier */

$this->title = 'Create Tb Supplier';
$this->params['breadcrumbs'][] = ['label' => 'Tb Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-supplier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
