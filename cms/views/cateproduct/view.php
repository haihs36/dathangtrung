<?php

    use yii\helpers\Html;
    use yii\widgets\DetailView;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCateproduct */

    $this->title = $model->title;
    $this->params['breadcrumbs'][] = ['label' => 'Tb Cateproducts', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-cateproduct-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->category_id], ['class' => 'btn btn-primary']) ?>
        <?=
            Html::a('Delete', ['delete', 'id' => $model->category_id], [
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
                'category_id',
                'title',
                'image',
                'fields:ntext',
                'slug',
                'tree',
                'lft',
                'rgt',
                'depth',
                'order_num',
                'status',
            ],
        ]) ?>

</div>
