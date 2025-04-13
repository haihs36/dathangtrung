<?php

    use yii\helpers\Html;


    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCarousel */

    $this->title = 'Create Tb Carousel';
    $this->params['breadcrumbs'][] = ['label' => 'Tb Carousels', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-carousel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
        $this->render('_form', [
            'model' => $model,
        ]) ?>

</div>
