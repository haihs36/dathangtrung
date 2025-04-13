<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Bag */

$this->title = 'Tạo bao';
$this->params['breadcrumbs'][] = ['label' => 'Danh sách bao', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bag-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
