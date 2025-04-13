//var getvMaxQty = '9999';

jQuery(document).ready(function ($) {
    $('#btn-search-order').on('click', function (e) {
        e.preventDefault();
        var status = $('#tbordersearch-status').val();
        $('#order-search-form').attr('action', "/don-hang" + (status ? '-' + status : '')).submit();
    });

    var startDate = $('#startDate');
    var endDate = $('#endDate');
    if (startDate.length && endDate.length) {
        startDate.datepicker({
            format: 'dd/mm/yyyy'
        });
        endDate.datepicker({
            format: 'dd/mm/yyyy'
        });
    }

    $(document).on('click', '.number-spinner span.btn', function () {
        var btn = $(this),
            oldValue = btn.closest('.number-spinner').find('input').val().trim(),
            newVal = 0;

        if (btn.attr('data-dir') == 'up') {
            newVal = parseInt(oldValue) + 1;
        } else {
            if (oldValue > 1) {
                newVal = parseInt(oldValue) - 1;
            } else {
                newVal = 1;
            }
        }

        btn.closest('.number-spinner').find('input').val(newVal);
        btn.closest('.calculate').find('.qty').trigger('blur');
    });

});

var users = {

    loadModel: function () {
        $("#myModal").modal({backdrop: 'static', keyboard: false});
    },
    closePopup: function () {
        $('.login-pop-dialog .close').on('click', function () {
            $('.login-pop-dialog').hide();
            window.location = '/';
        });
    },
    addCart: function (wrappers, add_buttons, max_fields) {
        // var max_fields      = 3; //maximum input boxes allowed
        var wrapper = $(wrappers); //Fields wrapper
        var add_button = $(add_buttons); //Add button ID

        var x = 1; //initlal text box count
        $(add_button).click(function (e) { //on add input button click
            e.preventDefault();
            if (x <= max_fields) { //max input box allowed
                x++; //text box increment
                var bg_item = (x%2) ? 'odd':'even';
                var $html = '<tr class="rowItem  '+ bg_item +'">\n' +
                    '                        <td valign="middle">\n' +
                    '                            <div class="img text-center" style=" overflow: hidden; position: relative;">\n' +
                    '                                <input type="hidden" name="sanpham_item['+x+'][img]" value="">\n' +
                    '                                <input type="file" name="sanpham_item['+x+'][img]" onchange="main.readURL(this,\'img'+x+'\')" aria-required="true">\n' +
                    '                                <a href="javascript:void(0)" data-id="img'+x+'">\n' +
                    '                                   <img src="/images/upload.png" style="max-width:130px;height: auto">\n' +
                    '                                    <br><span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span>\n' +
                    '                                </a>\n' +
                    '                            </div>\n' +
                    '                        </td>\n' +
                    '                        <td><input required class="form-control pitem" placeholder="Link sản phẩm" type="text" id="sanpham-item-'+x+'-link" value="" name="sanpham_item['+x+'][link]"></td>\n' +
                    '                        <td><input required class="form-control pitem" placeholder="Tên sản phẩm" type="text" id="sanpham-1-tensanpham" name="sanpham_item['+x+'][tensanpham]"></td>\n' +
                    '                        <td><input required class="form-control pitem" placeholder="Màu sắc" type="text" id="sanpham-'+x+'-color" name="sanpham_item['+x+'][color]"></td>\n' +
                    '                        <td><input required class="form-control pitem" placeholder="Kích thước" type="text" id="sanpham-'+x+'-size" name="sanpham_item['+x+'][size]"></td>\n' +
                    '                        <td><input required type="number" class="form-control pitem text-center item-qty allownumeric" min="1" value="1" placeholder="Số lượng" onblur="users.update_qty(1)" id="sanpham-item-'+x+'-qty" name="sanpham_item['+x+'][qty]"></td>\n' +
                    '                        <td><input required class="form-control item-price pitem"  placeholder="Đơn giá" type="text" id="sanpham-item-'+x+'-price" value="" name="sanpham_item['+x+'][price]"></td>\n' +
                    '                        <td> <textarea class="form-control" placeholder="Ghi chú..." id="sanpham-item-'+x+'-mota" name="sanpham_item['+x+'][mota]"></textarea></td>\n' +
                    '                        <td class="text-center"><a class="icon-delete remove_field btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a></td>\n' +
                    '                    </tr>';

                $(wrapper).append($html); //add input box
            } else {
                alert("Hệ thống chỉ cho phép thêm tối đa " + (max_fields + 1) + ' sản phẩm.');
                return false;
            }
        });

        $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
            e.preventDefault();
            if (x === 1) return false;
            $(this).parents('.rowItem').remove();
            x--;
        });
    },
    update_qty: function (id) {
        var $ = jQuery;
        //$("#loading").show();
        _qty = $("#sanpham-item-" + id + "-qty").val().replace(',', '');
        _price = $("#sanpham-item-" + id + "-price").val().replace(',', '');
        // Thanh tien
        _tigia = $("span.ti-gia").html();
        _thanhtien = parseFloat(_price) * parseInt(_qty) * parseInt(_tigia);

        $("#sanpham-item-" + id + "-tongtien").val(number_format(_thanhtien, 0, '', ','));
        _total = 0;
        _dem = 0;
        _totalQty = 0;

    },
    /*process input number quantity*/
    validQuantity: function ($tags) {
        var inputNumber = $($tags);

        inputNumber.blur(function() {
            var $this = $(this);
            var $val = $this.val();

            if (!$.isNumeric($val)) {
                $this.val(1);
            }
            var max = 200;
            if (($val < 1 || $val > max) && $val.length != 0) {
                if ($val < 1) {
                    $this.val(1);
                }

                if ($val > max) {
                    $this.val(max);
                }
                $('p.number-valid').fadeIn(2000, function () {
                    $(this).fadeOut(500);
                })
            }
        }).focus(function () {

        });
    },
    validMoney: function ($tags) {
        var inputNumber = $($tags);
        inputNumber.blur(function() {
            $('label.warning').remove();
            var $this = $(this);
            var $val = $this.val();

            if (!$.isNumeric($val)) {
                // $this.after('<label class="warning red">Số tiền phải là kiểu số</label>');
                // return false;
            }
        }).focus(function () {

        });
    }

};