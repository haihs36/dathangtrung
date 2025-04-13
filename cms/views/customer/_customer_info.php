<?php if($info){ ?>
<table class="table table-striped detail-view">
    <tbody>
    <tr>
        <th class="text-right">Tài khoản</th>
        <td><b><?= $info['fullname'] ?></b>(<i><?= $info['username'] ?></i>)</td>
    </tr>
    <tr>
        <th class="text-right">Điện thoại:</th>
        <td><b><?= $info['phone'] ?></b></td>
    </tr>
    <tr>
        <th class="text-right">Email:</th>
        <td><b><?= $info['email'] ?></b></td>
    </tr>
    <tr>
        <th class="text-right">Địa chỉ:</th>
        <td><b><?= $info['billingAddress'] ?></b></td>
    </tr>
    <tr>
        <th class="text-right">Số dư VĐT:</th>
        <td><b class="vnd-unit"><?= number_format($info['totalResidual']) ?> <em>đ</em></b></td>
    </tr>
    </tbody>
</table>
<?php } ?>