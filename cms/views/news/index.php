<?php

use yii\helpers\Html;
use kartik\grid\GridView;
$this->title = 'Danh sách tin tức';
$this->params['breadcrumbs'][] = $this->title;
?>
<p class="text-right">
    <?= Html::a('Tạo mới', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
</p>
<div class="boxd">

    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => false,
            'responsive'=>true,
            'toolbar' => [
                ['content'=>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                //            '{export}',
            ],
            'panel' => [
                'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i> Quản lý tin tức </h3>',
                'type' => GridView::TYPE_PRIMARY,
                'showFooter'=>true
            ],
            'responsive'=>true,
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                /*[
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 6%;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'attribute'         => 'news_id',
                        'format'        => 'raw',
                        'value'         => function ($model) {
                            return $model->news_id;
                        }
                ],*/
                [
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                        'contentOptions' => ['class' => 'text-center'],
                       // 'label'         => 'Image',
                        'format'        => 'raw',
                        'value'         => function ($model) {
                            return $model->getImageHtml();
                        }
                ],
                [
                        'attribute'      => 'title',
                        'filter'         => Html::input('text', 'TbNewsSearch[title]', isset($params['TbNewsSearch']['title']) ? $params['TbNewsSearch']['title'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                        'format'         => 'raw',
                        'value'          => function ($model) {
                            return $model->getTitleLink();
                        }
                ],
                [
                        'label'      => 'Chuyên mục',
                        'attribute'      => 'category_id',
                         'headerOptions'  => ['class' => 'text-centers', 'style' => 'width: 15%;'],
//                         'contentOptions' => ['class' => 'text-center'],
                         'filter'         => Html::dropDownList('TbNewsSearch[category_id]', isset($params['TbNewsSearch']['category_id']) ? $params['TbNewsSearch']['category_id'] : '',\common\components\CategoryModel::getDropdownCategories(), ['prompt' => '-- Chuyên mục --', 'class' => 'form-control', 'style' => 'width:100%;display:block;']),
                         'format'         => 'raw',
                         'value'          => function ($model) {
                             return isset($model->category->title) ? $model->category->title : null;
                         }
                ],
                [
                        'headerOptions' => ['class' => 'text-center', 'style' => 'width: 6%;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'attribute'         => 'Xem',
                        'filter'=>'',
                        'format'        => 'raw',
                        'value'         => function ($model) {
                            return $model->view;
                        }
                ],
                [
                        'attribute'         => 'Nổi bật',
                        'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 13%;'],
                        'contentOptions' => ['class' => 'text-center status'],
                        'filter'         => Html::dropDownList('TbNewsSearch[is_hot]', isset($params['TbNewsSearch']['is_hot']) ? $params['TbNewsSearch']['is_hot'] : '', ['' => '- Trạng thái -', 1 => 'Nổi bật', 0 => 'Tin thường'], ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
                        'format'         => 'raw',
                        'value'          => function ($model) {
                             return $model->getHotHtml();
                        }
                ],
                [
                    'attribute'          => 'status',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 13%;'],
                    'contentOptions' => ['class' => 'text-center status'],
                    'filter'         => Html::dropDownList('TbNewsSearch[status]', isset($params['TbNewsSearch']['status']) ? $params['TbNewsSearch']['status'] : '', ['' => '- Trạng thái -', 1 => 'Kích hoạt', 0 => 'Khóa'], ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->getStatusHtml();
                    }
                ],
                [
                   // 'label'          => 'Thao tác',
                    'headerOptions'  => ['class' => 'align-right', 'style' => 'width: 8%;'],
                    'contentOptions' => ['class' => 'align-right'],
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->getAction();
                    },
                ],
            ],
        ]); ?>
    </div>
</div>

