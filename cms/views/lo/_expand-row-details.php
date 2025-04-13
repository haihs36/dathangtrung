<div class="rows">
        <div style="width: 100%;" class="grid-view pd10">
            <table class="table table-bordered table-condensed table-hover small kv-table text-center">
                <tbody>
                <tr class="success">
                    <th colspan="12" class="text-center text-success">Danh sách mã vận đơn</th>
                </tr>
                <tr class="active">
                    <th class="text-center">STT</th>
                    <th>Mã Vận Đơn</th>
                    <th>MĐH</th>
                    <th>Cân nặng</th>
                    <th>Dài</th>
                    <th>Rộng</th>
                    <th>Cao</th>
                    <th>Cân quy đổi</th>
                    <th>Cân tính tiền</th>
                    <th>Phí</th>
                    <th>Ghi chú</th>
                    <th class="text-right">Thời gian</th>
                </tr>
                <?php if($data){
                    foreach ($data as $k=> $item){
                        ?>
                        <tr>
                            <td class="text-center"><?= ($k+1) ?></td>
                            <td><?= $item['transferID'] ?></td>
                            <td>
                                    <b><?= $item['identify'] ?></b>
                            </td>
                            <td><?= $item['kg'] ?> kg</td>
                            <td><?= $item['long'] ?></td>
                            <td><?= $item['wide'] ?></td>
                            <td><?= $item['high'] ?></td>
                            <td><?= $item['kgChange'] ?> kg</td>
                            <td><?= $item['kgPay'] ?> kg</td>
                            <td class="vnd-unit red"><?= number_format($item['totalPriceKg']) ?> đ</td>
                            <td><?= $item['note'] ?></td>
                            <td class="text-right"><?= !empty($item['payDate']) ? date('d/m/Y H:i',strtotime($item['payDate'])) :
                            (isset($create) ? date('d/m/Y H:i',strtotime($create)) : '') ?></td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
</div>