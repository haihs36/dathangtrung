<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model cms\models\TbNews */

$this->title = 'Create user';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="tbl-user-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?=
    $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
