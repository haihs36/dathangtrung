<?php
use yii\helpers\Html;
$uLogin = Yii::$app->user->identity;
$orderStatus = Yii::$app->controller->orderStatus;
$totalResidual = isset($uLogin->accounting) ? $uLogin->accounting->totalResidual : 0;
$complainStatus = Yii::$app->controller->complainStatus;
$cusId = \Yii::$app->user->id;
$total_dang_dat_thieu = \common\models\TbOrders::find()->where(['customerID' => $cusId,'status'=>[2,3,4,8,11]])->sum('debtAmount');
$total_kho_vn_thieu = \common\models\TbOrders::find()->where(['customerID' => $cusId,'status'=>9])->sum('debtAmount');

$allCoc = \common\models\TbOrders::find()->where(['customerID' => $cusId,'status'=>1])->asArray()->all();
$total_coc = 0;
if($allCoc){
    foreach ($allCoc as $item){
        $perCent    = \common\components\CommonLib::getPercentDeposit($item['totalOrder'],$item['customerID'],$item['deposit']);
        $coc_money  = ($item['totalOrder'] * $perCent / 100);
        $total_coc += $coc_money;
    }
}


$setting = Yii::$app->controller->setting;

$this->title = 'Hệ thống quản lý đặt hàng';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['orders/index']];
?>

<div class="row">

    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <div class="form-group">
                    <label>Chờ đặt cọc</label><br>
                    <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                        <?= $orderStatus[1] ?> đơn
                    </h4>
                </div>
                <div class="form-group">
                    <label>Số tiền cần cọc</label><br>
                    <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                        <?= number_format($total_coc)  ?> <em>đ</em>
                    </h4>
                </div>
            </div>
            <div class="icon"><i class="fa fa-usd"></i></div>
            <a href="/don-hang-1" class="small-box-footer">Xem chi tiết
                <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <div class="form-group">
                    <label>Đơn hàng đang đặt</label><br>
                    <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                        <?= $orderStatus[11] + $orderStatus[2] + $orderStatus[3] + $orderStatus[4] + $orderStatus[8] ?> đơn
                    </h4>
                </div>
                <div class="form-group">
                    <label>Tổng tiền còn thiếu</label><br>
                    <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                        <?=  number_format($total_dang_dat_thieu)  ?> <em>đ</em>
                    </h4>
                </div>
            </div>
            <div class="icon"><i class="fa fa-files-o"></i></div>
            <a href="/don-hang" class="small-box-footer">Xem chi tiết
                <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-blue">
            <div class="inner">
                <div class="form-group">
                    <label>Kho vn nhận</label><br>
                    <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                        <?= $orderStatus[9] ?> đơn
                    </h4>
                </div>
                <div class="form-group">
                    <label>Tổng tiền còn thiếu</label><br>
                    <h4 class="text-bold ng-binding" style="margin: 0px;font-size: 16px;">
                        <?=  number_format($total_kho_vn_thieu)  ?>
                    </h4>
                </div>
            </div>
            <div class="icon">
                <i class="ion fa fa-fw fa-database"></i>
            </div>
            <a href="/don-hang-9" class="small-box-footer">Xem chi tiết
                <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h4><?= $complainStatus[0] ?> đơn</h4>
                <p>Khiếu nại</p>
                <br>
            </div>
            <div class="icon">
                <i class="fa fa-frown-o"></i>
            </div>
            <a href="/danh-sach-khieu-nai" class="small-box-footer">Xem chi tiết
                <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">

      <!-- PRODUCT LIST -->
      <div class="box box-primary">
        <div class="box-header with-border text-center bg-purple">
          <h3 class="box-title">THÔNG BÁO MỚI</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div><!-- /.box-header -->
        <div class="box-body" style="height: 315px;max-height: 315px;overflow-y: auto;">
            <?php echo isset($setting['alert_system']) ? trim(Html::decode($setting['alert_system'])) : ''; ?>
        </div><!-- /.box-body -->

      </div><!-- /.box -->
    </div><!-- /.col -->
    <div class="col-md-8">
      <!-- TABLE: LATEST ORDERS -->
      <div class="box box-info">
        <div class="box-header with-border text-center bg-orange">
          <h3 class="box-title ">TIN NHẮN HỆ THỐNG</h3>
          <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
          </div>
        </div><!-- /.box-header -->
      <div class="box-body" style="height: 265px;max-height: 265px;overflow-y: auto;">
          <div class="table-responsive">
            <table class="table no-margin" id="table-sms">
              <thead>
                <tr>
                  <th style="width: 10%">Mã đơn</th>
                  <th style="width: 15%">Người gửi</th>
                  <th>Nội dung</th>
                  <th style="width: 12%">Thời gian</th>
                   <th style="width: 12%">Trạng thái</th>
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($dataSet)){
                    foreach ($dataSet as $key => $value) {
                ?>
                <tr>
                  <td><a target="_blank" href="/don-hang-view-<?php echo $value['order_id'] ?>"><?php echo $value['identify'] ?></a></td>
                  <td><?php echo $value['fullname'] ?></td>
                  <td><?php echo $value['message'] ?></td>
                  <td><?php echo date('d/m/Y H:i:s',strtotime($value['timestamp'])) ?></td>
                  <td class="sms-status">
                    <?php echo ($value['status'] == 1)?  'Đã xem' :'Chưa xem'?>
                  </td>
                </tr>
               <?php } } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div><!-- /.box-body -->
        <div class="box-footer clearfix">
          <a href="javascript:void(0);" class="btn btn-sm btn-info btn-flat pull-right">Đánh dấu đã đọc</a>
        </div><!-- /.box-footer -->
      </div><!-- /.box -->
    </div><!-- /.col -->
  </div>



