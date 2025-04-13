<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model cms\models\ConsignmentDetail */

$this->title = 'Create Consignment Detail';
$this->params['breadcrumbs'][] = ['label' => 'Consignment Details', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="consignment-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
