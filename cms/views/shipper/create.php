<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Tbshippers */

$this->title = 'Create Tbshippers';
$this->params['breadcrumbs'][] = ['label' => 'Tbshippers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbshippers-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
