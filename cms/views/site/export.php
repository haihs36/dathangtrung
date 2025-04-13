<?php
    header("Pragma: public");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: pre-check=0, post-check=0, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Content-Transfer-Encoding: none");
    header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
    header("Content-type: application/x-msexcel");
    header("Content-Disposition: attachment; filename=list-customer.xls");
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body style="font-family: arial;font-size: 10px;">
<table border="1" width="100%">
    <thead>
    <tr style="background-color: #4B4949;color: #FFFFFF;font-weight: bold;">
        <th>STT</th>
        <th>Tên khách hàng</th>
        <th>Email</th>
        <th>SĐT</th>
        <th>Tên sản phẩm</th>
        <th>sourceName</th>
        <th>SL</th>
        <th>Giá</th>
        <th>Image</th>
        <th>Link</th>
        <th>color</th>
        <th>size</th>
    </tr>
    </thead>
    <tbody>
    <?php if($customer){
        $i = 1;
        foreach ($customer as $key => $value) {
            $product = isset($value['product']) ? $value['product'] : [];
            if(!empty($product)){

                foreach ($product as $item){
            ?>
            <tr>
                <td valign="middle"><?php echo $i; ?></td>
                <td valign="middle"><?php echo $value['fullname'] ?></td>
                <td valign="middle"><?php echo $value['email'] ?></td>
                <td valign="middle"><?php echo $value['phone'] ?></td>
                <td valign="middle"><?php echo $item['name'] ?></td>
                <td valign="middle"><?php echo $item['sourceName'] ?></td>
                <td valign="middle"><?php echo $item['quantity'] ?></td>
                <td valign="middle"><?php echo $item['unitPrice'] ?></td>
                <td valign="middle"><?php echo $item['image'] ?></td>
                <td valign="middle"><?php echo $item['link'] ?></td>
                <td valign="middle"><?php echo $item['color'] ?></td>
                <td valign="middle"><?php echo $item['size'] ?></td>
                <?php
                    $i++;
                } } ?>
            </tr>
            <?php } } ?>
    </tbody>
</table>
</body>
</html>