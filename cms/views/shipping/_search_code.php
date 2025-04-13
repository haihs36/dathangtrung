<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Bắn mã vận đơn</h3>
    </div>
    <div class="box-body">
        <div class=" text-center">
            <form id="barcode-form" class="form-horizontal" action="/shipping/barcode" method="post">
                <input type="hidden" name="_csrf" value="ifys8bIFAhEvUFGtko-i8EcDiJy7AaII6Z1OO3nlBrWbIyS8FNhIr8fPqesfit5r_OhhZ8xQtfO5XOCnDvYEHw==">

                <input style="width:50%" type="text" id="orderNumber" class="form-control" name="TbShippingSearch[barcode]" value="" placeholder="Nhập mã VĐ....">
                <button type="submit" class="btn btn-primary btn-submitCode">Xác nhận</button>

            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('input[id=orderNumber]').focus();


        $('body').on('click', '.btn-submitCode', function (e) {
            Main.searchBareCode();

            return false;

        });

        $('#orderNumber').keypress(function (e) {
            if (e.which == '13') {
                Main.searchBareCode();
                return false;
            }
        });

    });
</script>