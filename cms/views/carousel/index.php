<?php

    use yii\helpers\Html;
    use yii\grid\GridView;

    /* @var $this yii\web\View */
    /* @var $searchModel cms\models\TbCarouselSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = 'Tb Carousels';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-carousel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= \common\widgets\Alert::widget() ?>
    <br/>

    <p>
        <?php echo Html::a('Danh sách', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Thêm ảnh', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions'=>function($model){
                    return ['class' => 'danger'];
            },
            'tableOptions' => [
                'class' => 'table-hover table-condensed',
            ],
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 15%;'],
                    'label'         => 'Image',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                            return $model->getImageHtml();
                        }
                ],
                [
                    'attribute'      => 'Tiêu đề',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 50%;'],
                    'contentOptions' => ['class' => ' '],
                    'filter'         => Html::input('text', 'TbCarouselSearch[title]', isset($params['TbCarouselSearch']['title']) ? $params['TbCarouselSearch']['title'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                            return $model->getTitleLink();
                        }
                ],
                [
                    'attribute'      => 'Trạng thái',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'contentOptions' => ['class' => 'text-center status'],
                    'filter'         => Html::dropDownList('TbCarouselSearch[status]', isset($params['TbCarouselSearch']['status']) ? $params['TbCarouselSearch']['status'] : '', ['' => '- Tất cả -', 1 => 'Kích hoạt', 0 => 'Khóa'], ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                            return $model->getStatusHtml();
                        }
                ],
                [
                    'attribute'      => 'Hành Động',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'filterOptions'  => ['class' => 'text-center'],
                    'filter'         => '',
                    'format'         => 'raw',
                    'value'          => function ($model) {
                            return $model->getAction();
                        },
                ],
                //            'carousel_id',
//            'text:ntext',
                // 'order_num',
                // 'status',

//            ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

</div>
