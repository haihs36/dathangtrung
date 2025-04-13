<div class="box-step-order">
    <div class="stepwizard">
        <div class="stepwizard-row">
            <div class="stepwizard-step">
                <span class="badge <?php echo (($status == 1) ? 'bg-blue-active': 'bg-aqua') ?> ">1</span>
                <p>Chờ đặt cọc</p>
            </div>
            <div class="stepwizard-step">
                <span class="badge <?php echo (($status == 11) ? 'bg-blue-active': 'bg-aqua') ?>">2</span>
                <p>Đã đặt cọc</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 2) ? 'bg-blue-active': 'bg-aqua') ?> " >3</span>
                <p>Đang đặt hàng</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 3) ? 'bg-blue-active': 'bg-aqua') ?> " >4</span>
                <p>Đã đặt hàng</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 4) ? 'bg-blue-active': 'bg-aqua') ?> " disabled="disabled">5</span>
                <p> Shop xưởng giao</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 8) ? 'bg-blue-active': 'bg-aqua') ?> " disabled="disabled">6</span>
                <p> Kho TQ nhận</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 9) ? 'bg-blue-active': 'bg-aqua') ?> " disabled="disabled">7</span>
                <p> Kho VN nhận</p>
            </div>
             <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 6) ? 'bg-blue-active': 'bg-aqua') ?> " disabled="disabled">8</span>
                <p> Đã trả hàng</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 5) ? 'bg-blue-active': 'bg-aqua') ?> " disabled="disabled">9</span>
                <p> Đã hủy</p>
            </div>
        </div>
    </div>
</div>
