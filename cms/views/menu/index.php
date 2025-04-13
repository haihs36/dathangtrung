<?php

    use yii\helpers\Html;
    use yii\grid\GridView;
    $this->title = 'Danh sách menu';
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header text-right">
            <?= Html::a('Tạo mới', ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{items}<div class="panel-body pull-right">{pager}{summary}',
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'rowOptions'=>function($model, $key, $index, $grid){
                $block = ($model->status==0 ? ' smooth':'');
                $class = '';
                if($index % 2 == 1){
                    $class = 'bg';
                }
                return ['class' => $class.''.$block];
                /*if($index % 2 == 1){
                    return ['class' => 'bg'];
                }*/
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label'         => 'Tiêu đề',
                    'filter'        => Html::input('text', 'TbMenuSearch[title]', isset($params['TbMenuSearch']['title']) ? $params['TbMenuSearch']['title'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Từ khóa tìm kiếm...']),
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return $model->getTitleLink();
                    }
                ],
                [
                    'label'          => 'Trạng thái',
                    'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 15%;'],
                    'contentOptions' => ['class' => 'text-center status'],
                    'filter'         => Html::dropDownList('TbMenuSearch[status]', isset($params['TbMenuSearch']['status']) ? $params['TbMenuSearch']['status'] : '', ['' => '- Trạng thái -', 1 => 'Kích hoạt', 0 => 'Khóa'], ['class' => 'form-control', 'style' => 'width:100%;display:block;']),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->getStatusHtml();
                    }
                ],
                [
                    'label'          => 'Thao tác',
                    'headerOptions'  => ['class' => 'align-right', 'style' => 'width: 20%;'],
                    'contentOptions' => ['class' => 'align-right'],
                    //                                'filterOptions'  => ['class' => 'text-center'],
                    // 'filter'         => \app\components\CommonLib::DropDownList('TbMenuSearch[parent_id]', \app\models\TbMenu::find()->sort()->all(), isset($params['TbMenuSearch']['parent_id']) ? $params['TbMenuSearch']['parent_id'] : ''),
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return $model->getAction();
                    },
                ],
            ],
        ]); ?>
    </div>
</div>



