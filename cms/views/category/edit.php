<?php

    use yii\helpers\Html;

    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCategory */

    $this->title = 'Chỉnh sửa chuyên mục: ' . ' ' . $model->title;
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách chuyên mục', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['Xem', 'id' => $model->category_id]];
    $this->params['breadcrumbs'][] = 'Chỉnh sửa';
?>
<div class="Tb-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', ['model' => $model, 'parent' => $parent]) ?>

</div>
