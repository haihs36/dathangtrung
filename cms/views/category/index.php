<?php

    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;

    /* @var $this yii\web\View */
    /* @var $searchModel cms\models\TbCategorySearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = 'Chuyên mục';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="Tb-category-index">

    <h1><?php Html::encode($this->title); ?></h1>

    <p>
        <?php echo Html::a('Danh sách chuyên mục', ['index'], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Tạo chuyên mục', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
               /*'category_id',
                [
                    'label'         => 'Image',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'format'        => 'raw',
                    'value'         => function ($model) {
                            return $model->getImageHtml();
                        }
                ],*/
                [
                    'attribute'     => 'Tiêu đề',
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 50%;'],
                    'filter'        => Html::input('text', 'TbCategorySearch[title]', isset($params['TbCategorySearch']['title']) ? $params['TbCategorySearch']['title'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                    'format'        => 'raw',
                    'value'         => function ($model) {
                            return $model->getTitleLink();
                        }
                ],
                [
                    'attribute'      => 'Trạng thái',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 15%;'],
                    'contentOptions' => ['class' => 'text-center status'],
                    'filter'         => Html::dropDownList('TbCategorySearch[status]', isset($params['TbCategorySearch']['status']) ? $params['TbCategorySearch']['status'] : '', ['' => '- Trạng thái -', 1 => 'Kích hoạt', 0 => 'Khóa'], ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
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
                    'filter'         => \common\components\CommonLib::DropDownList('TbCategorySearch[parent_id]', \cms\models\TbCategory::find()->sort()->all(), isset($params['TbCategorySearch']['parent_id']) ? $params['TbCategorySearch']['parent_id'] : ''),
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
