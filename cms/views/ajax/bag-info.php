<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title shop-title">
            Danh sách mã vận đơn
        </h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
            <table class="table table-bordered table-condensed table-hover">
                <tbody>
                <tr class="active">
                    <th class="text-center">STT</th>
                    <th>Mã Vận Đơn</th>
                    <th width="8%" class="text-center">Dài</th>
                    <th width="8%" class="text-center">Rộng</th>
                    <th width="8%" class="text-center">Cao</th>
                    <th width="8%" class="text-center">Cân nặng</th>
                    <th width="8%" class="text-center">Cân quy đổi</th>
                    <th width="8%" class="text-center">Cân tính tiền</th>
                    <th width="10%" class="text-center">Thời gian</th>
                    <th width="10%" class="text-center">Trạng thái đóng bao</th>
                    <th width="10%" class="text-center">Thao tác</th>
                </tr>
                <?php if($data){
                    foreach ($data as $k=> $item){
                        ?>
                        <tr>
                            <td class="text-center"><?= ($k+1) ?></td>
                            <td><?= $item['transferID'] ?></td>
                            <td class="text-center"><?= $item['long'] ?></td>
                            <td class="text-center"><?= $item['wide'] ?></td>
                            <td class="text-center"><?= $item['high'] ?></td>
                            <td class="text-center"><?= $item['kg'] ?> kg</td>
                            <td class="text-center"><?= $item['kgChange'] ?> kg</td>
                            <td class="text-center"><?= $item['kgPay'] ?> kg</td>
                            <td class="text-center">
                                <?= !empty($item['createDate']) ? date('d/m/Y H:i',strtotime($item['createDate'])) : '' ?>
                            </td>
                            <td class="text-center">
                                <?= \common\components\CommonLib::bagStatus($item['status'])?>
                            </td>
                            <td class="text-center">
                                <?php if($item['status'] != 2){ ?>
                                    <a class="btn-sm btn-danger bag-del-item" href="javascript:void(0)" data-id="<?= $item['id'] ?>" title="Xóa" ><span class="glyphicon glyphicon-trash"></span></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
    </div>

</div>