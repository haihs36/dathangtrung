<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCarousel */

    $this->title = $model->title;
    $this->params['breadcrumbs'][] = ['label' => 'Tb Carousels', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-carousel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->carousel_id], ['class' => 'btn btn-primary']) ?>
        <?=
            Html::a('Delete', ['delete', 'id' => $model->carousel_id], [
                'class' => 'btn btn-danger',
                'data'  => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method'  => 'post',
                ],
            ]) ?>
    </p>

    <?=
        DetailView::widget([
            'model'      => $model,
            'attributes' => [
                'carousel_id',
                'image',
                'link',
                'title',
                'text:ntext',
                'order_num',
                'status',
            ],
        ]) ?>

</div>
