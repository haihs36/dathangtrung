<?php

    use yii\helpers\Html;


    /* @var $this yii\web\View */
    /* @var $model cms\models\TbCategory */

    $this->title = 'Tạo chuyên mục';
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách chuyên mục', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
        $this->render('_form', [
            'model'  => $model,
            'parent' => $parent
        ]) ?>

</div>
