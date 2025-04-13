<div class="row">
        <div style="width: 100%;" class="pd10">
            <table class="table table-bordered table-condensed table-hover small kv-table">
                <tbody>
                <tr class="success">
                    <th colspan="12" class="text-center text-success"><h3>Danh sách mã vận đơn</h3></th>
                </tr>
                <tr class="active">
                    <th class="text-center">STT</th>
                    <th>Mã Vận Đơn</th>
                    <th>Cân nặng</th>
                    <th>Dài</th>
                    <th>Rộng</th>
                    <th>Cao</th>
                    <th>Cân quy đổi</th>
                    <th>Cân tính tiền</th>
                    <th>Phí phụ thu</th>
                    <th>Cân nặng < 1.5kg</th>
                    <th>Ghi chú</th>
                    <th class="text-right">Thời gian</th>
                </tr>
                <?php if($data){
                    foreach ($data as $k=> $item){
                        ?>
                        <tr>
                            <td class="text-center"><?= ($k+1) ?></td>
                            <td><?= $item['barcode'] ?></td>
                            <td><?= $item['kg'] ?> kg</td>
                            <td><?= $item['long'] ?></td>
                            <td><?= $item['wide'] ?></td>
                            <td><?= $item['high'] ?></td>
                            <td><?= $item['kgChange'] ?> kg</td>
                            <td><?= $item['kgPay'] ?> kg</td>
                            <td><b class="vnd-unit"><?= number_format($item['incurredFee']) ?></b></td>
                            <td><b class="vnd-unit"><?= number_format($item['kgFee']) ?></b>kg</td>
                            <td><?= $item['note'] ?></td>
                            <td class="text-right"><?= date('d/m/Y H:i',strtotime($item['createDate'])) ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
</div>