<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbHistory */
$this->title = 'Lịch sử người dùng';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
?>

<div class="notify-alert">
    <?= app\widgets\Alert::widget() ?>
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
    <table class="table table-bordered table-hover dataTable">
        <tbody>
        <tr>
            <th>Nhân viên</th>
            <td><?= isset($model->user) ? $model->user->first_name.' '.$model->user->last_name : null ?></td>
        </tr>
        <tr>
            <th>Mã đơn hàng</th>
            <td><?= isset($model->order->identify) ? $model->order->identify : null ?></td>
        </tr>
        <tr>
            <th>Nội dung</th>
            <td><?= $model->content ?></td>
        </tr>
        <tr>
            <th>Ngày gửi</th>
            <td><?= date('d-m-Y H:i:s', strtotime($model->createDate)) ?></td>
        </tr>
        <?php if(!empty($model->orderID)){ ?>
        <tr>
            <th>Chi tiết đơn hàng</th>
            <td><a target="_blank"  title="Chi tiết đơn hàng" href="<?php echo \yii\helpers\Url::toRoute(['orders/view','id'=>$model->orderID]) ?>">Chi tiết đơn hàng</a></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>
</div>
