<?php

    use yii\helpers\Html;
    use yii\grid\GridView;

    /* @var $this yii\web\View */
    /* @var $searchModel cms\models\TbCateProductSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = 'Danh sách chuyên mục';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-cateproduct-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a('Danh sách', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Thêm mới', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
//                ['class' => 'yii\grid\CheckboxColumn'],
                /*[
                    'label'         => 'Image',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'format'        => 'raw',
                    'value'         => function ($model) {
                            return $model->getImageHtml();
                        }
                ],*/
                [
                    'label'     => 'Tiêu đề',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 30%;'],
                    'filter'        => Html::input('text', 'TbCateProductSearch[title]', isset($params['TbCateProductSearch']['title']) ? $params['TbCateProductSearch']['title'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                    'format'        => 'raw',
                    'value'         => function ($model) {
                            return $model->getTitleLink();
                        }
                ],
                [
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'filterOptions'  => ['class' => 'text-center'],
                    'filter'         => \common\components\CommonLib::DropDownList('TbCateProductSearch[parent_id]', \cms\models\TbCateProduct::find()->sort()->all(), isset($params['TbCateProductSearch']['parent_id']) ? $params['TbCateProductSearch']['parent_id'] : '', '-- Danh mục gốc --'),
                    'format'         => 'raw',
                ],
                [
                    'label'      => 'Trạng thái',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 15%;'],
                    'contentOptions' => ['class' => 'text-center status'],
                    'filter'         => Html::dropDownList('TbCateProductSearch[status]', isset($params['TbCateProductSearch']['status']) ? $params['TbCateProductSearch']['status'] : '', ['' => '- Tất cả -', 1 => 'Hoạt động', 0 => 'Khóa'], ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                            return $model->getStatusHtml();
                        }
                ],
              
                [
                    'label'      => 'Hành Động',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
                    'contentOptions' => ['class' => 'text-center'],
                    'filterOptions'  => ['class' => 'text-center'],
                    'format'         => 'raw',
                    'value'          => function ($model) {
                            return $model->getAction();
                        },
                ],
//            [
//                'class' => 'yii\grid\ActionColumn',
//                'header'=>'Hành động',
//                'headerOptions' => ['width' => '80'],
//                'template' => '{view} {update} {delete}{link}',
//            ],

//             'order_num',
                //'category_id',
//            'parent_id',
//             'depth',
//             'fields:ntext',
//             'slug',
//            'tree',
//            'lft',
//            'rgt',
            ],
        ]); ?>

</div>
