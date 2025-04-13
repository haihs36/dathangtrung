<div class="box-step-order">
    <div class="stepwizard">
        <div class="stepwizard-row">
            <?php if($status == 0){ ?>
            <div class="stepwizard-step">
                <span class="badge <?php echo (($status == 0) ? 'bg-blue-active': 'bg-aqua') ?>">0</span>
                <p>Giỏ hàng</p>
            </div>
            <?php }?>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 1) ? 'bg-blue-active': 'bg-aqua') ?>">1</span>
                <p>Đang xử lý</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 2) ? 'bg-blue-active': 'bg-aqua') ?>" >2</span>
                <p>Đang đặt hàng</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 3) ? 'bg-blue-active': 'bg-aqua') ?>" >3</span>
                <p>Đã đặt hàng</p>

            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 4) ? 'bg-blue-active': 'bg-aqua') ?>" disabled="disabled">4</span>
                <p> Hoàn thành</p>
            </div>
            <div class="stepwizard-step">
                <span  class="badge <?php echo (($status == 5) ? 'bg-blue-active': 'bg-aqua') ?>">5</span>
                <p>Đã hủy</p>
            </div>
        </div>
    </div>
</div>

<!--<div class="block-alert">
    <div class="alert alert-warning" role="alert">
        <strong>Chú ý:</strong>
        <p>
            Sản phẩm trong giỏ sẽ tự động xóa trong vòng 30 ngày. <br>
            Người bán trên website 1688.com thường có quy định về số lượng mua tối thiểu, bội số mỗi sản phẩm,
            giá trị đơn hàng tối thiểu và sẽ từ chối bán nếu không đáp ứng. Trong trường hợp đó chúng tôi sẽ hủy những đơn hàng này và không báo trước.
        </p>
    </div>
</div>-->