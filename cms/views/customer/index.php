<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TbCustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Quản lý khách hàng';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="tb-member-index">
    <div class="mb15 text-right">
        <?= Html::a('Tạo tài khoản', ['update'], ['class' => 'btn btn-success']) ?>
    </div>
    <!--    --><?php //echo \common\widgets\Alert::widget() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
        'responsive'   => true,
        'pjax'         => true,
        'toolbar'      => [
            ['content' =>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['customer/index'], ['data-pjax' => false, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
            ],
            // '{export}',
            '{toggleData}'
        ],
        'export' => false,
        'panel'        => [
            'heading'    => '<h3 class="panel-title"><i class="fa fa-fw fa-users" aria-hidden="true"></i> Danh sách khách hàng </h3>',
            'type'       => GridView::TYPE_PRIMARY,
            'showFooter' => true
        ],
        'columns'      => [
            /*[
                'class'         => 'yii\grid\SerialColumn',
                'header'        => 'TT',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
                'contentOptions' => ['class' => 'text-center'],
            ],*/
             /*[
                 'headerOptions' => ['class' => 'text-center','style' => 'width: 7%;'],
                 'contentOptions' => ['class' => 'text-center'],
                 'label'         => 'ID',
                 'format'        => 'raw',
                 'filter'         => Html::input('text', 'TbCustomerSearch[customerID]', isset($params['TbCustomerSearch']['customerID']) ? $params['TbCustomerSearch']['customerID'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                 'value'         => function ($model) {
                     return $model->id;
                 }
             ],*/
            'id',
            [
                'header'              => 'Tên đăng nhập',
                'contentOptions'      => ['data-th' => 'Tên đăng nhập'],
                'attribute'           => 'customerID',
//                'vAlign'              => 'middle',
//                'width'               => '180px',
                'value'               => function ($model) {
                    return $model->username . ((Yii::$app->user->identity->role == ADMIN) ? '<br/> pass:  <b>' . \common\components\CommonLib::decryptIt($model->password_hidden) . '</b>' : '');
                },
                'filterType'          => GridView::FILTER_SELECT2,
                'filter'              => \yii\helpers\ArrayHelper::map(\common\models\Custommer::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                'format'              => 'raw'
            ],
            [
                'header'              => 'Quản lý',
                'contentOptions'      => ['data-th' => 'Quản lý'],
                'attribute'           => 'userID',
                'vAlign'              => 'middle',
                'width'               => '180px',
                'value'               => function ($model) {
                    return isset($model->user->username) ? $model->user->username : null;
                },
                'filterType'          => GridView::FILTER_SELECT2,
                'filter'              => \yii\helpers\ArrayHelper::map(\common\models\User::find()->select(['id', 'username'])->where(['!=', 'username', 'admin'])->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions'  => ['placeholder' => 'Chọn..'],
                'format'              => 'raw'
            ],

            [
                'headerOptions'  => ['style' => 'width: auto;'],
                'contentOptions' => ['data-th' => 'Họ và tên'],
                'label'          => 'Họ và tên',
                'format'         => 'raw',
                'filter'         => Html::input('text', 'TbCustomerSearch[fullname]', isset($params['TbCustomerSearch']['fullname']) ? $params['TbCustomerSearch']['fullname'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                'value'          => function ($model) {
                    return $model->fullname;
                }
            ],

            [
                'headerOptions'  => ['style' => 'width: auto;'],
                'contentOptions' => ['data-th' => 'Điện thoại'],
                'label'          => 'Điện thoại',
                'filter'         => Html::input('text', 'TbCustomerSearch[phone]', isset($params['TbCustomerSearch']['phone']) ? $params['TbCustomerSearch']['phone'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->phone;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: auto;'],
                'label'         => 'Ngày tạo',
                'attribute'           => 'created_at',
                'format'        => 'raw',
                'value'         => function ($model)
                {
                    return date('m/d/Y H:i:s', $model->created_at);
                }
            ],[
                'attribute'           => 'totalResidual',
                'headerOptions' => ['style' => 'width: auto;','class'=>'text-center'],
                'contentOptions' => ['data-th' => 'số dư ví','class'=>'text-center'],
                'header'         => 'số dư ví',
                'format'        => 'raw',
//                'filter'        => false,
                'value'         => function ($model)
                {
                    return ' <b class="vnd-unit">' . (number_format($model->totalResidual)) . '</b>';
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 20%;'],
                'label'         => 'Địa chỉ',
//                    'filter'         => Html::input('text', 'TbCustomerSearch[billingAddress]', isset($params['TbCustomerSearch']['billingAddress']) ? $params['TbCustomerSearch']['billingAddress'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                'format'        => 'raw',
                'value'         => function ($model) {
                    return $model->billingAddress;
                }
            ],
            [
                'label'          => 'Thao tác',
                'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 10%;'],
                'contentOptions' => ['class' => 'text-center'],
                'filterOptions'  => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                },
            ],
        ],
    ]); ?>

</div>
