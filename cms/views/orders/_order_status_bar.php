<div class="box-step-order">
    <div class="stepwizard">
        <div class="stepwizard-row">
             <!--<div class="stepwizard-step">
                <button type="button" class="btn <?php /*echo (($status == 7) ? 'btn-primary': 'btn-default') */?> btn-circle">1</button>
                <p>Chờ báo giá</p>
            </div>-->
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 1) ? 'btn-primary': 'btn-default') ?> btn-circle">1</button>
                <p>Chờ đặt cọc</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 11) ? 'btn-primary': 'btn-default') ?> btn-circle">2</button>
                <p>Đã đặt cọc</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 2) ? 'btn-primary': 'btn-default') ?> btn-circle" >3</button>
                <p>Đang đặt hàng</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 3) ? 'btn-primary': 'btn-default') ?> btn-circle" >4</button>
                <p>Đã đặt hàng</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 4) ? 'btn-primary': 'btn-default') ?> btn-circle" disabled="disabled">5</button>
                <p> Shop xưởng giao</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 8) ? 'btn-primary': 'btn-default') ?> btn-circle" disabled="disabled">6</button>
                <p> Đang vận chuyển</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 9) ? 'btn-primary': 'btn-default') ?> btn-circle" disabled="disabled">7</button>
                <p> Kho VN nhận</p>
            </div>
             <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 6) ? 'btn-primary': 'btn-default') ?> btn-circle" disabled="disabled">8</button>
                <p> Đã trả hàng</p>
            </div>
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($status == 5) ? 'btn-primary': 'btn-default') ?> btn-circle" disabled="disabled">9</button>
                <p> Đã hủy</p>
            </div>
        </div>
    </div>
</div>
