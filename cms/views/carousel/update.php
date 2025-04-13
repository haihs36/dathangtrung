<?php

    use yii\helpers\Html;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCarousel */

    $this->title = 'Update Tb Carousel: ' . ' ' . $model->title;
    $this->params['breadcrumbs'][] = ['label' => 'Tb Carousels', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->carousel_id]];
    $this->params['breadcrumbs'][] = 'Update';
?>
<div class="Tb-carousel-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
        $this->render('_form', [
            'model' => $model,
        ]) ?>

</div>
