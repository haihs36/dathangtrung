<?php
    $total_phutu = 0;
    $total_kg = 0;
    $total_price = 0;
?>
<link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="/css/layout.css" rel="stylesheet">
<div class="row">
        <div style="width: 100%;font-size: 14px" class="pd10">
            <table class="table table-bordered table-condensed table-hover">
                <tbody>
                <tr class="active">
                    <th class="text-center">STT</th>
                    <th>Mã Vận Đơn</th>
                    <th>Phụ thu (<b class="vnd-unit">VNĐ</b>)</th>
                    <th>Lý do<br/>phụ thu</th>
                    <th>Cân nặng</th>
                    <th>Đơn giá (<b class="vnd-unit">VNĐ</b>)</th>
                    <th>Thành tiền (<b class="vnd-unit">VNĐ</b>)</th>
                </tr>
                <?php if($data){
                    foreach ($data as $k=> $item){
                        $totalPrice = $discountKg*$item['kgPay'] + $item['incurredFee'];

                        $total_price += $totalPrice;
                        $total_kg += $item['kgPay'];
                        $total_phutu += $item['incurredFee'];
                        ?>
                        <tr>
                            <td class="text-center"><?= ($k+1) ?></td>
                            <td><?= $item['barcode'] ?></td>
                            <td><b class="vnd-unit"><?= number_format($item['incurredFee']) ?></b> </td>
                            <td><?= $item['note'] ?></td>
                            <td><?= $item['kgPay'] ?> kg</td>
                            <td><b class="vnd-unit"><?= number_format($discountKg) ?></b> </td>
                            <td><b class="vnd-unit"><?= number_format($totalPrice) ?></b> </td>
                        </tr>
                    <?php } } ?>
                </tbody>
                <tfoot>
                    <tr style="font-size: 18px">
                        <td class="text-center" colspan="2"><b>TỔNG</b></td>
                        <td><b class="vnd-unit"><?= number_format($total_phutu) ?></b> </td>
                        <td colspan="3" class="text-center"><b><?= $total_kg ?></b> kg</td>
                        <td><b class="vnd-unit"><?= number_format($total_price) ?></b> </td>
                    </tr>
                </tfoot>
            </table>
        </div>
</div>