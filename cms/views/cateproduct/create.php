<?php

    use yii\helpers\Html;


    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCateproduct */

    $this->title = 'Thêm mới';
    $this->params['breadcrumbs'][] = ['label' => 'Thêm mới', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-cateproduct-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
        $this->render('_form', [
            'model'  => $model,
            'parent' => $parent,
        ]) ?>

</div>
