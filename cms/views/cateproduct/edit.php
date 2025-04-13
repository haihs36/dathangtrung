<?php

    use yii\helpers\Html;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCateproduct */

    $this->title = 'Chỉnh sửa: ' . ' ' . $model->title;
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->category_id]];
    $this->params['breadcrumbs'][] = 'Cập nhất';
?>
<div class="Tb-cateproduct-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
        $this->render('_form', [
            'model'  => $model,
            'parent' => $parent,
        ]) ?>

</div>
