<?php
    use yii\helpers\Html;
    use kartik\grid\GridView;
    use yii\widgets\ActiveForm;
    use yii\helpers\Url;
    $this->title = 'QL đơn khiếu nại';
    $this->params['breadcrumbs'][] = $this->title;

    $complain = \common\models\TbComplain::find()->select(['id', 'status'])->asArray()->all();
    $complainStatus = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0];
    if ($complain) {
        foreach ($complain as $item) {
            if ($item['status'] == 1) $complainStatus[1]++;
            if ($item['status'] == 2) $complainStatus[2]++;
            if ($item['status'] == 3) $complainStatus[3]++;
            if ($item['status'] == 4) $complainStatus[4]++;
            $complainStatus[0]++;
        }
    }

    $businuss = \common\components\CommonLib::listUser(0, [ADMIN, WAREHOUSE, WAREHOUSETQ]);

?>
<div class="menu-complain">
    <ul class="step-action">
        <li class="<?php echo ($status==1) ? 'active' :''  ?>">
            <a href="<?php echo Url::toRoute(['complain/index','status'=>1]) ?>" class="active">Chờ xử lý<span class="badge <?= $complainStatus[1] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $complainStatus[1] ?></span></a>
        </li>
        <li class="<?php echo ($status==4) ? 'active' :''  ?>">
            <a href="<?php echo Url::toRoute(['complain/index','status'=>4]) ?>" class="active">Đang xử lý<span class="badge <?= $complainStatus[4] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $complainStatus[4] ?></span></a>
        </li>
        <li class="<?php echo ($status==2) ? 'active' :''  ?>">
            <a href="<?php echo Url::toRoute(['complain/index','status'=>2]) ?>" class="active">Đã xử lý<span class="badge b <?= $complainStatus[2] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $complainStatus[2] ?></span></a>
        </li>
        <li class="<?php echo ($status==3) ? 'active' :''  ?>">
            <a href="<?php echo Url::toRoute(['complain/index','status'=>3]) ?>" class="active">Đã hủy<span class="badge <?= $complainStatus[3] > 0 ? 'bg-red' : 'bg-aqua' ?>" style="margin-left: 5px"> <?= $complainStatus[3] ?></span></a>
        </li>
    </ul>
</div>

<div class="tb-complain-search">
    <?php $form = ActiveForm::begin([
        'id'      => 'complain-search-form',
        'action'  => ['index'],
        'method'  => 'get',
    ]); ?>


    <div class="rows">
        <div class="form-text ext-full">
            <?php echo  $form->field($searchModel, 'orderID')->textInput(['placeholder' => 'Mã đơn hàng', 'class' => 'form-control'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($searchModel, 'startDate',[
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full">
            <?= $form->field($searchModel, 'endDate',[
                'template' => '{label}<div class="input-group"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
            ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label(false) ?>
        </div>
        <div class="form-text ext-full ">
            <?php
            echo $form->field($searchModel, 'businessID')->dropDownList(\common\components\CommonLib::listUser(0,[ADMIN,WAREHOUSE,WAREHOUSETQ]), [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn nhân viên'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full ">
            <?php
            $customers = \yii\helpers\ArrayHelper::map(\common\models\TbCustomers::find()->select(['id', 'username'])->where(['status' => 1])->all(), 'id', 'username');
            echo $form->field($searchModel, 'customerID')->dropDownList($customers, [
                'class' => 'form-control select2', 'style' => 'width:100%', 'prompt' => '', 'data-placeholder' => 'Chọn khách hàng'])->label(false);
            ?>
        </div>
        <div class="form-text ext-full">
            <?php
                $searchModel->status = $searchModel->status > 0 ? $searchModel->status : 0;
                echo $form->field($searchModel, 'status')->dropDownList(\common\models\TbComplain::getStatus(), [
                    'class' => 'form-control select2','style'=>'width:100%','prompt'=>'','data-placeholder'=>'Trạng thái đơn'])->label(false);
            ?>
        </div>
        <button type="submit" id="btn-search-order" name="op" value="Tìm kiếm" class="btn btn-primary btn-flat">
            <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
        </button>
    </div>
    <!-- finish -->

    <?php ActiveForm::end(); ?>
</div>
<br>
<div class="box">
    <div class="box-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'toolbar' => [
                ['content'=>
                     Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['orders/index'], ['data-pjax'=>false, 'class' => 'btn btn-default', 'title'=>'Reset Grid'])
                ],
                //            '{export}',
            ],
            'panel' => [
                'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-file" aria-hidden="true"></i>'.$this->title.'</h3>',
                'type' => GridView::TYPE_PRIMARY,
                'showFooter'=>true
            ],
            'responsive'=>true,
            'tableOptions' => ['class' => 'table table-bordered table-hover dataTable', 'id' => 'tbl_manager'],
            'columns' => [
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'contentOptions' => ['class' => 'text-center'],
                     'label'         => 'Image',
                    'format'        => 'raw',
                    'value'         => function ($model) {
                        return \common\components\CommonLib::getImage($model->image, 80, 80);
                    }
                ],
                [
                    'header'      => 'Mã ĐH',
                    'format'         => 'raw',
                    'value'          => function ($model) {
                        return '<b>'.$model->order->identify.'</b><br/><a target="_blank" href="/orders/' . $model->order->orderID . '" title="Xem đơn hàng" class="xemdonhang"><i class="fa fa-eye" aria-hidden="true"></i> Xem đơn hàng</a>';
                    }
                ],
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'label'  => 'Khách hàng',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        return $model->customName;
                    }
                ],
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'label'  => 'Quản lý',
                    'format' => 'raw',
                     'value'         => function ($model) use ($businuss) {
                          return (isset($businuss[$model->businessID]) ?  '<label class="label label-success"> <i>KD:'.$businuss[$model->businessID].'</i></label>' : '').
                                (isset($businuss[$model->orderStaff]) ?  '<br/><label class="label label-danger"> <i>ĐH:'.$businuss[$model->orderStaff].'</i></label>' : '');
                       // return $model->username;

                       // return $model->username;
                    }
                ],
                [
                    'label'  => 'Loại khiếu nại',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        return $model->complainType->name;
                    }
                ],
               /* [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'label'  => 'Số tiền',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        return (!empty($model->compensation) ? number_format($model->compensation): 0).'<em>đ</em>';
                    }
                ],*/
                [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'label'  => 'Trạng thái',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        return $model->getStatusText($model->status);
                    }
                ],
                [
                    'headerOptions' => ['style' => 'width: 10%;'],
                    'label'  => 'Ngày gửi',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        return date('d/m/Y H:i:s', strtotime($model->create_date));
                    }
                ],
                [
                    'headerOptions' => ['class' => 'text-center', 'style' => 'width: 10%;'],
                    'label'  => 'Thao tác',
                    'format' => 'raw',
                    'value'  => function ($model) {
                        return $model->getAction();
                    },
                ],
            ],
        ]); ?>
    </div>
</div>

