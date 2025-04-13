<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TbTransfercode */

$this->title = 'Create Tb Transfercode';
$this->params['breadcrumbs'][] = ['label' => 'Tb Transfercodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-transfercode-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
