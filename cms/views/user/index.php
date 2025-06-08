<?php

    use yii\helpers\Html;
    use kartik\grid\GridView;
    /* @var $this yii\web\View */
    /* @var $searchModel common\models\UserSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */
    $this->title = 'Quản lý nhân viên';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="mb15 text-right">
<!--    --><?php //cho Html::buttonInput('Xóa tất cả', ['class' => 'delete_btn btn btn-info','data-url'=>\yii\helpers\Url::toRoute(['/user/del-all'])]);?>
    <?= Html::a('Tạo tài khoản', ['add'], ['class' => 'btn btn-success']) ?>
</div>
<div class="tb-member-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'tableOptions' => [
            'id'    => 'tbl_manager',
            'class' => 'table-hover'
        ],
        'responsive'=>false,
        'toolbar' => [
            ['content'=>
                 Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
            ],
        ],

        'columns' => [
            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'label'         => 'avatar',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return '<img width="60" height="60" class="img-thumbnail img-circle"  src="'.(!empty($model->userDetail->photo) ? Yii::$app->params['FileDomain'].$model->userDetail->photo : Yii::$app->homeUrl.'images/'.USER_PROFILE_IMAGES_DIRECTORY.'/'.USER_PROFILE_DEFAULT_IMAGE) .'">';
                }
            ],
            [
                'header'        => 'Vai trò',
                'attribute' => 'role',
                'vAlign' => 'middle',
                'width' => '180px',
                'value' => function ($model) {
                    return $model->role ? \common\components\CommonLib::getListRole($model->role) : null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \common\components\CommonLib::getListRole(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Chọn..'],
                'format' => 'raw'
            ],
            [
                'header'        => 'Tên đăng nhập',
                'attribute' => 'id',
                'vAlign' => 'middle',
                'width' => '180px',
                'value' => function ($model) {
                    $username = $model->username;
                    $fullname = $model->fullname;
                    if(Yii::$app->user->identity->role == ADMIN && Yii::$app->user->identity->username == 'admin'){
                        $username .= '<br/>password: <b>'.\common\components\CommonLib::decryptIt($model->password_hidden).'</b>';
                    }
                    return $fullname.'<br/>'.$username;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->select(['id','username'])->where(['!=','username','admin'])->all(), 'id', 'username'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Chọn..'],
                'format' => 'raw'
            ],
            [
                'headerOptions' => ['style' => 'width: 20%;'],
                'header'         => 'Họ và tên',
                'format'        => 'raw',
                'filter'         =>false,//Html::input('text', 'UserSearch[last_name]', isset($params['UserSearch']['last_name']) ? $params['UserSearch']['last_name'] : '', ['class' => 'form-control', 'style' => 'display:inline-block;margin-left: 5px', 'placeholder' => 'Tìm kiếm']),
                'value'         => function ($model) {
                    return $model->first_name.' '.$model->last_name;
                }
            ],

            [
                'headerOptions' => ['style' => 'width: 15%;'],
                'label'         => 'Điện thoại',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return isset($model->userDetail->cellphone) ? $model->userDetail->cellphone : null;
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'label'         => 'Ngày tạo',
                'format'        => 'raw',
                'value'         => function ($model) {
                    return date('d-m-Y',$model->created_at);
                }
            ],
            [
                'headerOptions' => ['style' => 'width: 10%;'],
                'label'         => 'Hoạt động',
                'format'        => 'raw',
                'value'         => function ($model) {
                    $statusClass = Html::encode($model->status == ACTIVE) ? 'glyphicon glyphicon-ok' : 'glyphicon glyphicon-ban-circle';
                    return Html::a('<span class="'.$statusClass.'"></span>', 'javascript:void(0)', ['class'=>'ableToChangeStatus btn btn-default', 'id'=>'ableToChangeStatus'.$model->id, 'url'=>\yii\helpers\Url::to([Yii::$app->controller->id."/status"]), 'title'=>($model->status) == ACTIVE ? 'Khóa' : 'Kích hoạt']);
                }
            ],

            [
                'label'      => 'Thao tác',
                'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 10%;'],
                'contentOptions' => ['class' => 'text-center'],
                'filterOptions'  => ['class' => 'text-center'],
                'format'         => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                },
            ],
        ],
    ]);?>
</div>
