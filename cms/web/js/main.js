function removeCharacter(inputString) {
    return inputString.replace(/([~!@#$%^&*()_+=`{}\[\]\|\\:;'<>,.\/? ])+/g, '').replace(/^(-)+|(-)+$/g, '');
}

function focusOrderNumber() {
    var idOrder = document.getElementById("orderNumber");
    if (idOrder !== null) {
        idOrder.value = "";
        idOrder.focus();
    }
}

function copy($id) {
    var textarea = document.getElementById($id);
    textarea.select();
    document.execCommand("copy");
}

/*var format = function (num) {
    var str = num.toString().replace("$", ""), parts = false, output = [], i = 1, formatted = null;
    if (str.indexOf(".") > 0) {
        parts = str.split(".");
        str = parts[0];
    }
    str = str.split("").reverse();
    for (var j = 0, len = str.length; j < len; j++) {
        if (str[j] != ",") {
            output.push(str[j]);
            if ((i % 3) == 0 && j < (len - 1)) {
                output.push(",");
            }
            i++;
        }
    }
    formatted = output.reverse().join("");
    return (formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
};*/
function readURL(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('[data-id="' + id + '"]').children('img').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function delete_order_item(productId) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không ?')) {
        $.ajax({
            url: '/orders/removeitem',
            data: {'productId': productId},
            type: 'POST',
            success: function (data) {
                window.location.reload(true);
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    }
}

function setModalMaxHeight(element) {
    this.$element = $(element);
    this.$content = this.$element.find('.modal-content');
    var borderWidth = this.$content.outerHeight() - this.$content.innerHeight();
    var dialogMargin = $(window).width() < 768 ? 20 : 60;
    var contentHeight = $(window).height() - (dialogMargin + borderWidth);
    var headerHeight = this.$element.find('.modal-header').outerHeight() || 0;
    var footerHeight = this.$element.find('.modal-footer').outerHeight() || 0;
    var maxHeight = contentHeight - (headerHeight + footerHeight);

    this.$content.css({
        'overflow': 'hidden'
    });

    this.$element
        .find('.modal-body').css({
        'max-height': maxHeight,
        'overflow-y': 'auto'
    });
}

$('.modal').on('show', function () {
    $(this).show();
    $('.modal-dialog').css({
        width: 'auto'
    });
    setModalMaxHeight(this);
});

$(window).resize(function () {
    if ($('.modal.in').length == 1) {
        setModalMaxHeight($('.modal.in'));
    }
});

jQuery(document).ready(function ($) {
    var $ = jQuery;

    $('input[type=checkbox]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue', //icheckbox_square-blue
        radioClass: 'iradio_minimal-blue',
        increaseArea: '20%' /* optional */
    });

    $('ul.nav li.dropdown').hover(function () {
        $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
    }, function () {
        $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
    });
});

$(function () {

    Main.saveCode(); //luu tung ma van don khi tra hang
    Main.saveCodeShipper();
    Main.removeBarcode();
    Main.removeCodeShip();
    Main.sendMessage();

    $('.option-image').hide();


    
    $.each($('#tbcomplain-type input:radio'), function (u, v) {
        if ($(this).is(':checked')) {
            var radio = $(this);
            var type = radio.val();
            $.ajax({
                url: '/ajax-check',
                type: 'post',
                dataType: "json",
                data: {id: $('#orderID').val(), type: type},
                beforeSend: function () {
                    $('.form-wrapper').html('');
                    radio.after('<div class="ajax-progress"><div class="waiting">&nbsp;</div><div class="message">Vui lòng đợi...</div></div>');
                },
                success: function (data) {
                    $('.ajax-progress').remove();
                    $('.form-wrapper').fadeIn(500).html(data);
                },
                error: function () {
                    $('.ajax-progress').remove();
                }
            });
        }
    });

    $(document).on('click', '#tbcomplain-type input:radio', function (e) {
        var radio = $(this);
        var type = radio.val();
        $.ajax({
            url: '/ajax-check',
            type: 'post',
            dataType: "json",
            data: {id: $('#orderID').val(), type: type},
            beforeSend: function () {
                $('.form-wrapper').html('');
                radio.after('<div class="ajax-progress"><div class="waiting">&nbsp;</div><div class="message">Vui lòng đợi...</div></div>');
            },
            success: function (data) {
                $('.ajax-progress').remove();
                $('.form-wrapper').fadeIn(500).html(data);
            },
            error: function () {
                $('.ajax-progress').remove();
            }
        });
    });


    if ($('input:radio[name=rdoimg]').is(':checked')) {
        $checked = $("input:radio[name=rdoimg]:checked").val();
        $('#' + $checked).show();
        $(".fileUp").attr("disabled", 'disabled');
    }

    $('.currency').formatCurrency();
    // $('.divide').divide();

    /*$(".currency").keyup(function (e) {
        $(this).val(format($(this).val()));
    });
    $('.currency').each(function (u, v) {
        $(this).val(format($(this).val()));
        $price = parseInt($(this).text(), 10);
        $(this).html(format($price));
    });*/
    //
    $(".select2").select2({
        placeholder: function () {
            $(this).data('placeholder');
        },
        allowClear: true
    });

    var hash = window.location.hash;
    $(hash).css({
        'color': 'red',
        'border': '1px solid red',
        'padding': '5px'
    });

    window.notify = new Notify();
    var selectedItems = [];

    var max_fields = 10; //maximum input boxes allowed
    var wrapper_mvd = $(".input_mvd_wrap"); //Fields wrapper
    var add_button_mvd = $(".add_mvd_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button_mvd).click(function (e) { //on add input button click
        e.preventDefault();

        var shopid = $(this).data('shopid');

        if (x < max_fields) { //max input box allowed
            x++; //text box increment
            $(this).closest('td').find('div.input_mvd_wrap').append(
                '<p class="clear"><label class="col-sm-4"><a href="#" class="remove_field"><i class="fa fa-fw fa-close"></i></a></label>' +
                '<label class="col-sm-8 txt-cont input_mvd_wrap">' +
                '<input class="form-control" placeholder="Nhập mã vận đơn" type="text" name="shop[' + shopid + '][mvd][]"> ' +
                '</label></p>'
            );
        }
    });

    $(wrapper_mvd).on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        $(this).parents('p').remove();
        x--;

    });


    $(document).on('click', 'table.sticky-table .form-textarea', function () {
        var $this = $(this);
        $id = $this.data('id');
        var img = $this.closest('tr').find(".file_img-" + $id).val();
        var checkbox = $this.closest('tr').find("div.hinh-anh input.form-checkbox");
        if ($(checkbox).is(':checked') && img) {
            $this.prop("readonly", 0);
        } else {
            $this.prop("readonly", 1);
        }
        return false;

    });

    var body = $('body');

    var img_id = 0;
    body.on('click', '.upload-anh', function () {
        img_id = $(this).data('id');
        $('#photo-file').trigger('click');
    });

    $(document).on('change', '#photo-file', function () {
        Main.readURL(this, img_id);
        var $this = $(this);
        var files = $this.prop('files');
        var file = files[0];
        if (/^image\/(jpeg|png|gif)$/.test(file.type)) {
            var formData = new FormData();
            formData.append('Photo[image]', file);
            formData.append('pid', img_id);
            $.ajax({
                url: $this.closest('form').attr('action'),
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                type: 'post',
                success: function (response) {
                    if (response.result === 'success') {
                        $('.file_img-' + img_id).val(response.photo.id);
                    } else {
                        alert(response.error);
                    }
                    if (typeof callback === 'function') callback();
                }
            });
        }
    });

    body.on('change', '#lo-customerid', function () {
        var customerID = $(this).val();
        Main.load_customer(customerID);
    });

    body.on('click', '.btn-delete', function () {
        if (confirm('Bạn có chắc chắn xóa mã vận đơn này không?')) {
            $.ajax({
                url: '/delete-consign-detail',
                data: {id: $(this).data('id')},
                type: 'post',
                dataType: 'json',
                success: function (rs) {
                    $('#myModal').modal('show');
                    $('.modal-container').html('<div style="text-align: center"><i class="fa fa-check-circle" style="font-size:24px;color:red"></i> Xóa dữ liêu </div>');
                    setTimeout(function () {
                        $('#myModal').modal('hide');
                    }, 3000);
                }
            });
        }

        return false;

    });
    //import
    body.on('click', '.btn-import', function (e) {
        e.preventDefault();
        $('#myModal').modal('show').find('.modal-title').text('Import shipping code');
        $.ajax({
            url: $(this).data('url'),
            type: 'get',
            success: function (model) {
                $('.modal-container').html(model);
            }
        });

    });
    //upload anh dai dien
    if ($('input:radio[name=rdoimg]').is(':checked')) {
        $checked = $("input:radio[name=rdoimg]:checked").val();
        $('#' + $checked).show();
        $(".fileUp").attr("disabled", 'disabled');
    }
    body.on('change', 'input:radio[name=rdoimg]', function () {
        $('.option-image').hide();
        if ($(this).val() === 'yes') {
            $('#yes').show();
            $(".fileUp").removeAttr("disabled");
        } else {
            $('#no').show();
            $(".fileUp").attr("disabled", 'disabled');
        }
    });
    body.on('click', '.select-on-check-all', function () {
        $(".grid-view input").prop('checked', $(this).prop('checked'));
    });
    //update order gridview
    body.on('click', '.update-order', function () {
        selectedItems = selectedItems.concat($('.grid-view').yiiGridView('getSelectedRows'));
        var obj = {};
        if (selectedItems.length) {
            $.each(selectedItems, function (i, v) {
                var value = $('.grid-view').find('input[name=order_' + v + ']').val();
                if (value && $.isNumeric(value)) {
                    obj[v] = value;
                }
            });
        } else {
            alert('Vui lòng chọn dữ liệu cần chỉnh sửa');
            return false;
        }

        if (!$.isEmptyObject(obj)) {
            $.ajax({
                url: '/tags/updateorder',
                data: {'data': obj},
                type: 'post',
                success: function (rs) {
                    if (rs.status) {
                        alert('update success');
                        location.reload();
                    }
                }
            });
        } else {
            // alert('Bạn chưa nhập dữ liệu cần chỉnh sửa');
        }
    });
    //payment shipper
    //
    body.on('click', '.btn-pay-shipper', function (e) {
        //e.preventDefault();
        var myArray = [];
        var kgPay = 0;

        $('#list-shop-result .shop-item input:checked').each(function () {
            var $items = $(this).closest('.items');

            var tran_id = $items.data('id');
            var kgP = parseFloat($items.find('.kgPay-' + tran_id).val());

            kgPay += kgP;
            myArray.push(tran_id);
        });


        if (myArray.length <= 0) {
            $('#myModal').modal('show').find('.modal-title').text('Thông báo');
            $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:24px;color:red"></i> Vui lòng chọn lưu tối thiểu 1 mã vận đơn để trả hàng. </div style="text-align: center">');
            setTimeout(function () {
                $('#myModal').modal('hide');
            }, 3000);
            return false;
        }
        if (!kgPay) {
            $('#myModal').modal('show').find('.modal-title').text('Thông báo');
            $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:20px;color:red"></i> Vui lòng nhập cân nặng tính tiền.</div>');
            setTimeout(function () {
                $('#myModal').modal('hide');
            }, 3000);
            return false;
        }

        //var form = $('#form');
        if (confirm('Bạn có chắc chắn muốn trả hàng cho các mã đã chọn không?')) {
            $.ajax({
                type: "POST",
                url: '/tra-hang-ky-gui',
                data: {
                    'tran_id': myArray,
                    'loID': $('#loID').val(),
                    'customerID': $('#customerID').val(),
                    'shipfee': $('#shipfee').val()
                },
                beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                dataType: 'json',
                success: function (rs) {
                    if (rs.success) {
                        $('.modal-container').html('<div style="text-align: center"><i class="fa fa-check-circle" style="font-size:24px;color:red"></i> Trả hàng thành công.</div>');
                        setTimeout(function () {
                            $('#myModal').modal('hide');
                        }, 2000);

                        window.location = '/chi-tiet-phieu-' + rs.loid;
                        return false;
                    } else {
                        $('.modal-container').html(rs.message);
                    }
                }
            });
        }
        // e.preventDefault(); // avoid to execute the actual submit of the form.
        return false;
    });
    //payment all for order
    body.on('click', '.btn-pay-all', function (e) {
        //e.preventDefault();
        var myArray = [];
        var order = [];

        $('#list-shop-result .shop-item input:checked').each(function () {
            var $items = $(this).closest('.items');

            var id = $items.data('id');
            var oid = $items.data('orderid');
            var sid = $items.data('sid');
            var barcode = $items.data('rel');

            if ($.inArray(oid, order) == -1) {
                order.push(oid);
            }
            myArray.push({
                tran_id: id,
                oid: oid,
                sid: sid,
                barcode: barcode
            });
        });

        var kgPay = parseFloat($('#totalKgPay').val());
        var debt = parseFloat($('#totalPay').val());


        if (myArray.length <= 0) {
            $('#myModal').modal('show').find('.modal-title').text('Thông báo');
            $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:24px;color:red"></i> Vui lòng chọn lưu tối thiểu 1 mã vận đơn để trả hàng. </div style="text-align: center">');
            setTimeout(function () {
                $('#myModal').modal('hide');
            }, 3000);
            return false;
        }
        if (!kgPay) {
            $('#myModal').modal('show').find('.modal-title').text('Thông báo');
            $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:24px;color:red"></i> Vui lòng nhập cân nặng tính tiền.</div>');
            setTimeout(function () {
                $('#myModal').modal('hide');
            }, 3000);
            return false;
        }

        //var form = $('#form');
        if (confirm('Bạn có chắc chắn muốn trả hàng cho các mã đã chọn không?')) {
            $.ajax({
                type: "POST",
                url: '/thanh-toan',
                data: {
                    'order': order,
                    'shipfee': $('#shipfee').val(),
                    'debt': debt,
                    'object': myArray,
                    'loID': $('#loID').val(),
                    'customerID': $('#customerID').val()
                },
                beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                dataType: 'json',
                success: function (rs) {
                    if (rs.success) {
                        $('.modal-container').html('<div style="text-align: center"><i class="fa fa-check-circle" style="font-size:24px;color:red"></i> Trả hàng thành công.</div>');
                        setTimeout(function () {
                            $('#myModal').modal('hide');
                            window.location = '/chi-tiet-phieu-' + rs.loid;
                        }, 1000);
                        return false;
                    } else {
                        $('.modal-container').html(rs.message);
                    }
                }
            });
        }
        // e.preventDefault(); // avoid to execute the actual submit of the form.
        return false;
    });
    //print
    body.on('click', '.btn-print', function (e) {
        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            beforeSend: function () {
                $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
            },
            dataType: 'json',
            success: function (rs) {
                $('#myModal').modal('hide');
                var contents = rs.data;

                var frame1 = document.createElement('iframe');
                frame1.name = "frame1";
                frame1.style.position = "absolute";
                frame1.style.top = "-1000000px";
                document.body.appendChild(frame1);
                var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
                frameDoc.document.open();
                frameDoc.document.write('<html><head><title></title>');

                // frameDoc.document.write('<style>table {  border-collapse: collapse;  border-spacing: 0; width:100%; margin-top:20px;} .table td, .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th{ padding:8px 18px;  } .table-bordered, .table-bordered > tbody > tr > td, .table-bordered > tbody > tr > th, .table-bordered > tfoot > tr > td, .table-bordered > tfoot > tr > th, .table-bordered > thead > tr > td, .table-bordered > thead > tr > th {     border: 1px solid #e2e2e2;} </style>');

                // your title
                frameDoc.document.title = rs.title;

                frameDoc.document.write('</head><body>');
                frameDoc.document.write(contents);
                frameDoc.document.write('</body></html>');
                frameDoc.document.close();
                setTimeout(function () {
                    window.frames["frame1"].focus();
                    window.frames["frame1"].print();
                    document.body.removeChild(frame1);
                }, 500);
                return false;

                // var WindowObject = window.open("", "PrintWindow", "top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
                // WindowObject.document.writeln(HTML);
                // WindowObject.document.close();
                // WindowObject.focus();
                //window.print();
                // WindowObject.close();
            }
        });
        return false;
    });
    //tinh tien
    body.on('click', '.btn-charged', function (e) {
        e.preventDefault();
        var searchIDs = $("input[class=shop-check-item]:checkbox:checked").map(function () {
            return this.value;
        }).toArray();

        if (searchIDs.length <= 0) {
            alert('Vui lòng chọn tối thiểu 1 shop để trả hàng.');
            $("html, body").animate({scrollTop: 0}, 600);
            return false;
        } else {
            var error = 0;
            $('.kgreq').each(function () {
                //kiem tra du lieu tung shop
                // var shop = $('.shop-' + id);
                // var kg = shop.find('.kg-' + id).val();
                $('.error').removeClass('error');
                var kg = $(this).val();
                if (kg == '') {
                    alert('Vui lòng nhập cân nặng');
                    $(this).val('').focus().addClass('error');
                    error = 1;
                    return false;
                }
            });

            if (!error) {
                var form = $('#form');
                $.ajax({
                    type: "POST",
                    url: form.attr('action'),
                    data: form.serialize() + '&type=update', // serializes the form's elements.
                    beforeSend: function () {
                        $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                        $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý</div>');
                    },
                    dataType: 'json',
                    success: function (rs) {
                        $('#myModal').modal('hide');
                        $('#list-shop-result').html(rs.data);
                        $('#ResultCurrency').show();

                        $('.currency').formatCurrency();
                        /*$('.currency').each(function (u, v) {
                            $(this).val(format($(this).val()));
                            $price = parseInt($(this).text(), 10);
                            $(this).html(format($price));
                        });*/

                        $("button.btn-pay-all").removeAttr("disabled");
                    }
                });
            }
        }

        return false;
    });
    //xoa shop
    body.on('click', '.btn-shop-delete', function () {
        var orderID = $(this).data('order');
        var shopID = $(this).data('shop');

        if (orderID && shopID) {
            if (confirm('Bạn có chắc chắn muốn xóa shop này không?')) {
                $.ajax({
                    url: '/paymemt/delete-shop',
                    data: {'orderID': orderID, 'shopID': shopID},
                    type: 'post',
                    success: function (rs) {
                        alert(rs.sms);
                        location.reload();
                    }
                });
            }
        }
    });
    //chon tat ca shop checkbox
    body.on('change', '.shop-check-all', function () {
        var checkboxes = $(this).closest('.shop-item').find('input.check-item:checkbox');
        if ($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });


    //chon tat ca shop btn
    body.on('click', '.check-all', function () {
        var checkboxes = $('.shop-item').find('input.shop-check-item:checkbox');
        if ($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
    body.on('change', '.form-select', function () {
        var value = $(this).val();

        if (value == 1) {
            $(this).closest('.shop-item').find('.btn-pay').show();
        } else {
            $(this).closest('.shop-item').find('.btn-pay').hide();
        }
    });
    //tinh phi kien
    /*body.on('click','.btn-save',function () {
        var mvd = $(this).data('code');
        var kg =  $('.kg-'+mvd).val();
        var long =  $('.long-'+mvd).val();
        var wide =  $('.wide-'+mvd).val();
        var high =  $('.high-'+mvd).val();
        var kgChange = parseFloat((long * wide * high)/6000); //can nang quy doi
        var kgPay = (kgChange > kg ? kgChange : kg);

        $.ajax({
            url: '/warehouse/update',

        });
    });*/
    //thanh toan shop
    body.on('click', '.btnUpdateOrder', function () {
        if (confirm('Bạn có chắc chắn muốn cập nhật lại đơn hàng này không?')) {
            var id = $(this).attr('rel');
            // var shop = $(this).closest('.shop-item');

            // var mvd = shop.find('#mvd-' + id).val();
            // var number = shop.find('#orderNumber-' + id).val();
            // var kg = shop.find('#kg-' + id).val();
            // var status = shop.find('#status-' + id).val();
            // var shipFee = shop.find('#shipFee-' + id).val();
            //
            // var kgFee = shop.find('input#kgFee-' + id + ':checked').val();
            // if (typeof kgFee === 'undefined') {
            //     kgFee = 0;
            // }

            //var incurredFee = shop.find('#incurredFee-' + id).val();
            //var shippingStatus = shop.find('#shippingStatus-' + id).val();
            var actual = parseFloat($('#actualPayment-' + id).val());
            var actualChina = parseFloat($('#actualChina-' + id).val());
            //var orderID = $(this).attr('order');
            // var note = shop.find('#note-' + id).val();


            // if (mvd == '') {
            //     alert('Vui lòng nhập mã vận đơn');
            //     shop.find('#mvd-' + id).focus();
            //     return false;
            // }
            // if (number == '') {
            //     alert('Vui lòng nhập mã order number');
            //     shop.find('#orderNumber-' + id).focus();
            //     return false;
            // }
            // if (kg == '') {
            //     alert('Vui lòng nhập cân nặng ');
            //     shop.find('#kg-' + id).val('').focus();
            //     return false;
            // }

            // if (status != 1) {
            //     alert('Để thực hiện trả hàng vui lòng đổi tình trạng hàng sang trạng thái đã thanh toán');
            //     shop.find('#status-' + id).focus();
            //     return false;
            // }
            if (actual == '' || actual == 0) {
                alert('Tiền thanh toán thực tế không được để trống và phải lớn hơn 0');
                // shop.find('#actualPayment-'+id).val('');
                $('#actualPayment-' + id).focus();
                return false;
            }

            // if (shippingStatus != 3) {
            //     alert('Hiện tại hàng của shop này vẫn chưa về kho VN vui lòng xác nhận lại');
            //     return false;
            // }

            /*if (actual > actualChina) {
                alert('Tiền thanh toán thực tế không được lớn hơn tiền hàng');
                $('#actualPayment-' + id).focus();
                //return false;
            }*/

            return true;
            /*$.ajax({
                url: baseUrl + 'orders/pay',
                type: 'post',
                data: {
                    suppID: id, orderID: orderID, mvd: mvd, number: number, status: status, note: note, kgFee: kgFee,
                    shipFee: shipFee, kg: kg, incurredFee: incurredFee, actual: actual, shippingStatus: shippingStatus
                },
                dataType: 'json',
                /!*beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + 'images/loader.gif"/></div>');
                    /!*if ($('.ajax-loader').text() == ''){
                        $('.ajax-loader').html('<img src="/images/loader.gif"/>');
                    }*!/
                },*!/
                success: function (rs) {
                    //$('#myModal').modal('hide');
                    //alert(rs.sms);
                    if (rs.success) {
                        window.location.url = rs.url;
                    } else {
                        $('.modal-container').html(rs.message);
                    }

                    // if(rs.success){
                    // $('.modal-container').html(rs.sms);
                    // setTimeout(function(){// wait for 5 secs(2)
                    //     location.reload(); // then reload the page.(3)
                    // }, 2000);
                    // }
                }
            });*/
        }

        return false;
    });
    //hoan lai tien
    body.on('click', '.return-bank', function () {
        var $this = $(this);

        var price = $this.data('price');
        var oid = $this.data('oid');
        var url = $this.data('url');
        if (confirm('Bạn có chắc chắn hoàn lại số tiền: ' + price.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + ' vào ví cho khách')) {
            if ($this.hasClass('active')) {
                return false;
            }

            $this.addClass('active');

            $.ajax({
                url: url,
                type: 'post',
                data: {price: price, oid: oid},
                dataType: 'json',
                beforeSend: function () {
                    $this.removeClass('return-bank').html('<div class="ajax-loading"><img src="' + baseUrl + '/images/loader.gif"/></div>');
                },
                success: function (rs) {
                    $this.removeClass('active');
                    $('.ajax-loading').remove();
                    notify.success(rs.sms);
                    if (rs.status) {
                        $('.box-wallet').remove();
                    } else {
                        $this.addClass('return-bank').html('Hoàn lại ví điện tử');
                    }
                    // setTimeout(function(){// wait for 5 secs(2)
                    //     location.reload(); // then reload the page.(3)
                    // }, 2000);
                    // }
                }
            });
        }
    });
    body.unbind('click', '.change_status');
    body.on('change', '.change_status', function () {
        var checkbox = $(this);

        var status = checkbox.attr('data-status');
        var name = checkbox.data('name');

        var id = checkbox.data('id');
        var url = checkbox.data('url');
        var tagId = checkbox.attr("id");

        if (status == 1) {
            $('label[for=status-name]').text(checkbox.data('off'));
        } else {
            $('label[for=status-name]').text(checkbox.data('on'));
        }
        $('label[for=name-cate]').text(name);

        $('#modal_status').modal({
            keyboard: false,
            backdrop: "static"
        });

        $('#dismiss-modal').unbind('click');
        $('#dismiss-modal').on('click', function (e) {
            if (status == 1)
                checkbox.prop('checked', true).change();
            else
                checkbox.prop('checked', false).change();

            $('#modal_status').modal('hide');
            return false;
        });

        $('#action-confirm-change-status').unbind('click');
        $('#action-confirm-change-status').on('click', function () {
            $.ajax({
                url: url,
                type: 'POST',
                data: {id: id, status: status},
                dataType: 'JSON',
                beforeSend: function (d) {
                    checkbox.addClass('disabled');
                },
                error: function (d) {
                    notify.error("Error request.");
                },
                success: function (rs) {
                    if (rs.result == 'success') {
                        checkbox.attr('data-status', rs.status);
                        notify.success(rs.message);
                        if (checkbox.is(':checked') && tagId == 'status') {
                            checkbox.parents('tr').find('.title').removeClass('smooth');
                            checkbox.closest('tr').removeClass('smooth');
                        } else if (tagId == 'status') {
                            checkbox.closest('tr').addClass('smooth');
                            checkbox.parents('tr').find('.title').addClass('smooth');
                        }
                    } else {
                        if (checkbox.is(':checked')) {
                            checkbox.prop('checked', false).change();
                        } else {
                            checkbox.prop('checked', true).change();
                        }
                        notify.error(rs.message);
                    }

                    checkbox.removeClass('disabled');
                    $('#modal_status').modal('hide');
                    return false;
                }
            });
        });
    });
    body.on('change', 'input:radio[name=rdoimg]', function () {
        $('.option-image').hide();
        if ($(this).val() === 'yes') {
            $('#yes').show();
            $(".fileUp").removeAttr("disabled");
        } else {
            $('#no').show();
            $(".fileUp").attr("disabled", 'disabled');
        }
    });
    body.on('click', '.confirm-delete', function () {
        var button = $(this).addClass('disabled');
        var title = button.attr('data-original-title');

        if (confirm(title ? 'Bạn có chắc chắn muốn xóa "' + title + '" này không ?' : 'Confirm the deletion')) {
            /*if (button.data('reload')) {
                return true;
            }*/
            $.getJSON(button.attr('href'), function (response) {
                if (response.result === 'success') {
                    notify.success(response.message);
                    button.closest('tr').fadeOut(function () {
                        this.remove();
                    });
                } else {
                    notify.error(response.message);
                }
            });
        }
        button.removeClass('disabled');
        return false;
    });
    body.on('click', '.ableToUpdateValue', function () {
        if (confirm('Are you sure ?')) {
            var id = $(this).attr('id');
            var recordId = id.split('ableToUpdateValue')[1];
            tinyMCE.triggerSave(false, true);
            var value = $('#setting-' + recordId).is(':checkbox') ? (($('#setting-' + recordId).is(':checked') == true) ? 1 : 0) : $('#setting-' + recordId).val();
            $.ajax({
                url: '/setting/edit',
                type: "POST",
                data: {id: recordId, value: value},
                dataType: 'json',
                beforeSend: function () {
                    $('.loading-img').show();
                },
                success: function (response) {
                    if (response.status == 'success') {
                        notify.success('Successfully Updated');
                    } else {
                        notify.error('Error');
                    }
                },
                complete: function () {
                    $('.loading-img').hide();
                },
                error: function () {
                    alert('There was a problem while updating setting. Please try again');
                }
            });
        }
    });
    body.on('click', '.move-up, .move-down', function (e) {
        e.preventDefault();
        var button = $(this).addClass('disabled');
        $.getJSON(button.attr('href'), function (response) {
            button.removeClass('disabled');
            if (response.result === 'success' && response.swap_id) {
                var current = button.closest('tr');
                var swap = $('tr[data-key=' + response.swap_id + ']', current.parent());
                if (swap.get(0)) {
                    if (button.hasClass('move-up')) {
                        swap.before(current);
                    } else {
                        swap.after(current);
                    }
                }
            }
            else if (response.error) {
                notify.error(response.error);
            }
        });

        return false;
    });
    body.bind('keydown', function (e) {
        if (e.ctrlKey && e.which === 83) { // Check for the Ctrl key being pressed, and if the key = [S] (83)
            $('.model-form').submit();
            e.preventDefault();
            return false;
        }
    });

    $('input.currency').on('input', function (e) {
        $(this).val(formatCurrency2(this.value.replace(/[,]/g, '')));
        $(this).val(formatCurrency2(this.value.replace(/[,]/g, '')));
    }).on('keypress', function (e) {
        if (!$.isNumeric(String.fromCharCode(e.which))) e.preventDefault();
    }).on('paste', function (e) {
        var cb = e.originalEvent.clipboardData || window.clipboardData;
        if (!$.isNumeric(cb.getData('text'))) e.preventDefault();
    });
});

(function ($) {
    $.fn.formatCurrency = function () {
        return $.each(this, function () {
            var value = 0;
            if ($(this).is('input')) {
                value = $(this).val();
                $(this).val(formatNumber(value.trim(), '.', ','));
            } else {
                value = $(this).text();
                $(this).text(formatNumber(value.trim(), '.', ','));
            }
        });
    };
})(jQuery);


function formatCurrency2(number) {
    var n = number.split('').reverse().join("");
    var n2 = n.replace(/\d\d\d(?!$)/g, "$&,");
    return n2.split('').reverse().join('');
}

function formatNumber(nStr, decSeperate, groupSeperate) {
    nStr += '';
    x = nStr.split(decSeperate);
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
    }
    return x1 + x2;
}


var Main = {

    payment_Transport: function () {
        $('body').on('click', '.btnHuy', function () {

            var item_id = $(this).data('id');

            swal({
                    title: "Thông báo",
                    text: "Bạn có chắc chắn muốn hủy không?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Có!",
                    cancelButtonText: "Không!",
                    closeOnConfirm: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: '/ajax/canceltsport',
                            type: "POST",
                            data: {id: item_id},
                            dataType: 'json',
                            // beforeSend: function () {
                            //     $('.loading-img').show();
                            // },
                            success: function (res) {
                                if(res.success){
                                    swal({
                                        title: "Hủy thành công",
                                        type: "success",
                                        confirmButtonClass: "btn-success",
                                        timer: 3000
                                    });

                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);
                                }else{
                                    swal("Hủy thất bại", "Có vấn đề xảy ra trong quá trình xử lý. Xin vui lòng thử lại", "error");
                                }

                            },
                            error: function () {
                                swal("Hủy thất bại", "Có vấn đề xảy ra trong quá trình xử lý. Xin vui lòng thử lại", "error");
                            }
                        });
                        //
                    }
                });

        });

        $('body').on('click', '.btnPay', function () {

            var item_id = $(this).data('id');

            swal({
                    title: "Thông báo",
                    text: "Bạn có chắc chắn muốn duyệt thanh toán này không?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Có!",
                    cancelButtonText: "Không!",
                    closeOnConfirm: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: '/ajax/paymenttransport',
                            type: "POST",
                            data: {id: item_id},
                            dataType: 'json',
                            // beforeSend: function () {
                            //     $('.loading-img').show();
                            // },
                            success: function (res) {
                                if(res.success){
                                    swal({
                                        title: "Duyệt thành công",
                                        type: "success",
                                        confirmButtonClass: "btn-success",
                                        timer: 3000
                                    });

                                    setTimeout(function() {
                                        location.reload();
                                    }, 3000);
                                }else{
                                    swal("Duyệt thất bại", "Có vấn đề xảy ra trong quá trình xử lý. Xin vui lòng thử lại", "error");
                                }

                            },
                            error: function () {
                                swal("Duyệt thất bại", "Có vấn đề xảy ra trong quá trình xử lý. Xin vui lòng thử lại", "error");
                            }
                        });
                        //
                    }
                });

        });
    },

    searchBareCode: function () {

        if($('.btn-submitCode').hasClass('loading')){
            $('#myModal').modal('show').find('.modal-title').text('Thông báo');
            $('.modal-container').html('<div style="text-align: center">Bạn thao tác quá nhanh</div>');

            setTimeout(function () {
                $('#myModal').modal('hide');
                $('form input[id=orderNumber]').focus();
            }, 1000);
            return false;
        }

        if($('#orderNumber').val() == ''){
            $('#myModal').modal('show').find('.modal-title').text('Thông báo');
            $('.modal-container').html('<div style="text-align: center">Vui lòng nhập mã vận đơn</div>');
            setTimeout(function () {
                $('#myModal').modal('hide');
                $('form input[id=orderNumber]').focus();
            }, 1000);

            return false;
        }

        $('.btn-submitCode').addClass('loading');

        $.ajax({
            url: $("#barcode-form").action,
            type: 'POST',
            dataType:"json",
            data: $("#barcode-form").serialize(),
            beforeSend: function () {
                $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
            },
        }).done(function (rs) {
            $('form input[id=orderNumber]').val('').focus();
            // $('#myModal').modal('hide');
            $('.btn-submitCode').removeClass('loading');

            $('.modal-container').css({
                'overflow': 'hidden',
                'width': 'auto',
                'min-height': '80px',
                'text-align': 'center'
            });
            $('.modal-container').html(rs.message);
            if(!rs.success){
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }

            // $('#result-search-barcode').html(rs.message);
            /* setTimeout(function () {
                 window.location.reload();
             }, 2000);*/

        });

        return false;

    },
    updateQuantityReceived:function(pid,qty,note){
            $.ajax({
                  url: '/ajax/quantity-received',
                  data: {'pid':pid,qty: qty,note: note},
                  type: 'post',
                  dataType: 'json',
                  success: function (rs) {
                      if(rs.success){
                          notify.success('Cập nhật thành công');
                          $('#sl_nhan_'+pid).html(rs.qty);
                      }
                  }
              });
    },
    loadImage: function (input, id) {
        var $ = jQuery;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('[data-id="' + id + '"]').children('img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    },

    insertBarcode: function () {
        var barcode = $("#orderNumber").val();
        $('.error').remove();
        if (barcode == '') {
            $("#orderNumber").after('<div class="error help-block">Mã vận đơn là bắt buộc</div>');
            $("#orderNumber").focus();
            return false;
        }

        if (barcode.length < 10) {
            $("#orderNumber").after('<div class="error help-block">Mã vận đơn không hợp lệ.</div>');
            $("#orderNumber").focus();
            return false;
        }
        $("#orderNumber").val('');
        var pid = $('#pxk').val();
        var cusID = $('#customer_id').val();

        if (pid) {
            $.ajax({
                url: '/update-barcode',
                type: 'post',
                data: {barcode: barcode, pid: pid, cusID: cusID},
                dataType: 'json',
                beforeSend: function () {
                    $('#myModal').modal('show');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/></div>');
                },
                success: function (rs) {
                    if (!rs.success) {
                        $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:20px;color:red"></i> ' + rs.message + '</div>');
                    } else {
                        $('#list-barcode').html(rs.html);
                        $('.modal-container').html('<div style="text-align: center"><h4 class="text-red"><i class="icon fa fa-check-circle"></i> Thêm mã thành công.</h4></div>');

                    }

                    setTimeout(function () {
                        $('#myModal').modal('hide');
                        $("#orderNumber").focus();
                    }, 3000);
                }
            });
        }
    },

    load_customer: function (customerID) {
        $.ajax({
            type: 'get',
            url: '/customer/info',
            data: {customerID: customerID},
            dataType: 'json',
            success: function (rs) {
                $('.cusomerInfo').html(rs.uDetail);
                $('#editor1').html(rs.address);
            }
        });
        return false;
    },
    loadShop: function (tags, uid, loid, status) {
        if (uid) {
            $.ajax({
                url: '/load-shop',
                data: {'uid': uid, 'loid': loid, 'status': status},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $(tags).show();
                    //$('#myModal').modal('show').find('.modal-title').text('Thông báo');
                    //$('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + 'images/loader.gif"/></div>');
                },
                success: function (rs) {
                    if (rs.empty) {
                        $('#btnOption').hide();
                    } else {
                        $('#btnOption').show();
                        $(tags).html(rs.data);
                        $('.currency').formatCurrency();
                        /*$(".currency").keyup(function (e) {
                            $(this).val(format($(this).val()));
                        });
                        $('.currency').each(function (u, v) {
                            $(this).val(format($(this).val()));
                            $price = parseInt($(this).text(), 10);
                            $(this).html(format($price));
                        });*/
                    }
                }
            });
        } else {
            $(tags).hide();
            $('#btnOption').hide();
        }
    },
    bagInit: function () {
        $('#bag_barcode').val('').focus();
        $('.btn-bag-submit').click(function () {
            Main.updateBage();
        });
        $("#bag_barcode").keyup(function (e) {
            if (e.which == 13) {
                Main.updateBage();
            }
        });

        Main.bagDelItem();

    },
    bagDelItem: function () {
      $('.bag-del-item').on('click',function () {
          $this =  $(this);
          if(confirm("Bạn có chắc chắn muốn xóa mã kiện này không?")) {
              var id = $(this).data('id');
              $.ajax({
                  url: '/ajax/bagdelitem',
                  data: {'id': id},
                  type: 'post',
                  dataType: 'json',
                  beforeSend: function () {
                      $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                      $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/></div>');
                  },
                  success: function (rs) {
                      var icon_ = '<i class=" fa fa-fw fa-close"></i>';
                      if (rs.success) {
                          icon_ = '<i class="fa fa-check-circle"></i>';
                      }
                      $('.modal-container').html('<div class="font24" style="text-align: center"><label class="font50 red-color">' + icon_ + '</label> ' + rs.msg + '</div>');
                      setTimeout(function () {
                          $('#myModal').modal('hide');
                      }, 1000);

                      $this.closest('tr').fadeOut(function () {
                          this.remove();
                      });
                  }
              });
          }

          return false;
      }) ;
    },
    bagChange: function ($this) {
        $parent = $($this).closest('form');

        var kg = parseFloat($parent.find('#bag-kg').val());
        var long = parseFloat($parent.find('#bag-long').val());
        var wide = parseFloat($parent.find('#bag-wide').val());
        var high = parseFloat($parent.find('#bag-high').val());
        var note = $parent.find('#bag-note').val();
        if(isNaN(kg)) {
            var kg = 0;
        }
        if(isNaN(long)) {
            var long = 0;
        }
        if(isNaN(wide)) {
            var wide = 0;
        }
        if(isNaN(high)) {
            var high = 0;
        }

        var kgChange = parseFloat((long * wide * high) / 6000); //can nang quy doi
            kgChange = kgChange.toFixed(2);

        var kgPay = (kgChange > kg ? kgChange : kg);
        var m3 = parseFloat(kgChange / 166);
            m3 = m3.toFixed(2);

        $('#bag-kgchange').val(kgChange);
        $('#bag-kgpay').val(kgPay);
        $('#bag-m3').val(m3);


        return false;
    },
    updateBage: function () {
        var bag_id = $('#bag_id').val();
        var barcode = $('#bag_barcode').val();
        if (barcode == '' || barcode == null) {
            alert('Vui lòng nhập mã vận đơn');
            $('#bag_barcode').focus();
            return false;
        }

        $.ajax({
            url: '/update-bag',
            data: {'barcode': barcode,'bagid':bag_id},
            type: 'post',
            dataType: 'json',
            beforeSend: function () {
               // $(tags).show();
                $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/></div>');
            },
            success: function (rs) {
                var icon_ = '<i class=" fa fa-fw fa-close"></i>';
                if(rs.success) {
                    $('#bag-result').html(rs.html);
                    icon_ = '<i class="fa fa-check-circle"></i>';
                }
                $('.modal-container').html('<div class="font24" style="text-align: center"><label class="font50 red-color">'+icon_+'</label> '+rs.msg+'</div>');
               setTimeout(function () {
                   $('#myModal').modal('hide');
                    $('#bag_barcode').val('').focus();
               }, 1000);

            }
        });
        return false;
    },
    kgChange: function ($this, mvd,type) {

        var kg = 0, long = 0, wide = 0, high = 0, quantity = 0;
        $parent = $($this).closest('tr.items');
        $parent_head = $($this).closest('.rows');

        var oid = $parent.data('orderid');
        var sid = $parent.data('sid');
        var cid = $parent.data('cid');

        kg = parseFloat($parent.find('.kg-' + mvd).val());
        long = parseFloat($parent.find('.long-' + mvd).val());
        wide = parseFloat($parent.find('.wide-' + mvd).val());
        high = parseFloat($parent.find('.high-' + mvd).val());
        quantity = parseInt($parent.find('.quantity-' + mvd).val());
        var note = $parent.find('.note-' + mvd).val();

        var kgChange = parseFloat((long * wide * high) / 6000); //can nang quy doi

        kgChange = parseFloat(kgChange.toFixed(2));
        $parent.find('.kgChange-' + mvd).val(kgChange);

        var kgPay = (kgChange > kg ? kgChange : kg);
        $parent.find('.kgPay-' + mvd).val(parseFloat(kgPay));
        var checkbox = 0;
        if ($parent.find('.checkbox-' + mvd).is(':checked')) {
            checkbox = $parent.find('.checkbox-' + mvd + ':checked').val();
        }

        if ($($this).hasClass('active')){
            return false;
        }


       // if(confirm('Bạn có chắc chắn muốn thay đổi nội dung này không?')) {
            $($this).addClass('active');
            $.ajax({
                url: '/update-kg',
                type: 'post',
                data: {
                    cid: cid,
                    kg: kg,
                    mvd: mvd,
                    long: long,
                    wide: wide,
                    high: high,
                    qty: quantity,
                    kgChange: kgChange,
                    kgPay: kgPay,
                    note: note,
                    oid: oid,
                    sid: sid,
                    id: mvd,
                    checked: checkbox,
                    type: type
                },
                dataType: 'json',
                beforeSend: function () {
                    // $('#myModal').modal('show').find('.modal-title').html('Thông báo');
                    // $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                success: function (rs) {
                   // $('#myModal').modal('hide');
                    $($this).removeClass('active');
                    /* $('.modal-container').html('<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-check-circle"></i></label>  Cập nhật thành công.</div>');
                     setTimeout(function () {
                         $('#myModal').modal('hide');
                     }, 1000);*/

                    if (rs.success) {
                        var data = rs.data;

                       // if (data.quantity) {
                            $parent_head.find('.th_total_quantity').html(data.quantity);
                       // }
                       // if (data.gtdh) {
                            $parent_head.find('.th_gtdh').html(formatNumber(data.gtdh, '.', ','));
                       // }
                        //if (data.totalWeight) {
                            $parent_head.find('.th_total_kg').html(data.totalWeight);
                       // }
                       // if (data.totalWeightPrice) {
                            $parent_head.find('.th_weight_price').html(formatNumber(data.totalWeightPrice, '.', ','));
                       // }
                       // if (data.totalPayment) {
                            $parent_head.find('.th_total_payment').html(formatNumber(data.totalPayment, '.', ','));
                       // }
                       // if (data.totalPaid) {
                            $parent_head.find('.th_totalPaid').html(formatNumber(data.totalPaid, '.', ','));
                       // }
                       // if (data.debtAmount) {
                            $parent_head.find('.th_debtAmount').html(formatNumber(data.debtAmount, '.', ','));
                            $parent_head.find('.th_phidonggo').html(formatNumber(data.phidonggo, '.', ','));
                       // }

                        var shop_vnd_total = 0;
                        var totalKg = 0;
                        var totalCheck = 0;
                        $.each($('input.barcode-check-item'), function () {
                            if (this.checked) {
                                totalKg += parseFloat($(this).closest('tr.items').find('.kgpay').val());
                                totalCheck++;
                            }
                        });


                        $.each($('.rows'), function (u, tag) {
                            if ($(tag).find('input.barcode-check-item').is(':checked')) {
                                var vnd_total = $(tag).find('.th_debtAmount').html();
                                if (vnd_total) {
                                    shop_vnd_total += parseFloat(vnd_total.replace(/,/g, ''));
                                }
                            }
                        });


                        //don ki gui
                        $parent.find('#kgfee-' + mvd).html(formatNumber(data.kgfee, '.', ','));
                        $parent.find('#priceKg-' + mvd).html(formatNumber(data.totalWeightPrice, '.', ','));
                        $('.totalPay-shippers').html(formatNumber(data.totalPayment, '.', ','));
                         totalKg = totalKg.toFixed(2);

                        $('.totalPay').html(formatNumber(shop_vnd_total, '.', ','));
                        $('.totalKgPay').html(totalKg);
                        $('#totalPay').val(shop_vnd_total);
                        $('#totalKgPay').val(totalKg);
                        $('.numCodeCheck').html(totalCheck);

                    }
                    if (rs.error === 3 || quantity === 0) {
                        $parent_head.find('.quantity-' + mvd).val(0);
                        $parent_head.find('.isCheck-' + mvd).html('');
                    }
                    if (rs.error === 0) {
                        $parent_head.find('.isCheck-' + mvd).html('<span class="btn bg-orange btn-xs ">Đã kiểm</span>');
                    }

                    notify.success(rs.message);
                }
            });

            return false;
       // }




    },
    saveCode: function () {
        $('body').on('click', '.btn-save-code', function (e) {
            e.preventDefault();
            var $this = $(this);
            var mvd = $this.data('code');
            $parent = $this.closest('tr');
            var kg = parseFloat($parent.find('.kg-' + mvd).val());
            /*if (!$('#barcode-' + mvd).is(':checked')) {
                 alert('Bạn chưa tích chọn mã để lưu.');
                 return false;
            }*/

            /*if (kg <= 0) {
                $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-warning"></i></label>Bạn chưa nhập cân nặng.</div>');
                setTimeout(function () {
                    $('#myModal').modal('hide');
                }, 1000);
                return false;
            }*/


            var oid = $this.data('oid');
            var sid = $this.data('sid');
            $parent.find('.barcode-' + mvd).iCheck('check');
            var long = parseFloat($parent.find('.long-' + mvd).val());
            var wide = parseFloat($parent.find('.wide-' + mvd).val());
            var high = parseFloat($parent.find('.high-' + mvd).val());
            var note = $parent.find('.note-' + mvd).val();

            var kgChange = parseFloat((long * wide * high) / 6000); //can nang quy doi
            kgChange = kgChange.toFixed(2);
            var kgPay = (kgChange > kg ? kgChange : kg);

            $.ajax({
                url: '/transfercode/save',
                type: 'post',
                data: {
                    kg: kg, mvd: mvd, long: long, wide: wide, high: high,
                    kgChange: kgChange, kgPay: kgPay, note: note, oid: oid, sid: sid
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                success: function (rs) {
                    $('.modal-container').html('<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-check-circle"></i></label>  Cập nhật thành công.</div>');
                    setTimeout(function () {
                        $('#myModal').modal('hide');
                    }, 1000);
                    //$('.modal-container').html('<div style="text-align: center">'+rs.message+'</div>');
                    // setTimeout(function() { $('#myModal').modal('hide'); }, 3000);
                    // window.location.reload();
                    if (rs.data) {
                        var data = rs.data;
                        $parent = $this.closest('.rows');
                        $parent.find('.totalkgFee').html(data.totalKg);
                        $parent.find('.weightPrice').html(data.weightPrice);
                        $parent.find('.totalPayment').html(data.totalPayment);
                        $parent.find('.totalPaid').html(data.totalPaid);
                        $parent.find('.debtAmount').html(data.debtAmount);
                        $parent.find('.kgPay-' + mvd).attr('kgPay', kgPay);
                        $parent.find('.barcode-' + mvd).attr('kgPay', kgPay);

                        $('.numCodeCheck').html(data.numCode);
                        $('.totalKgPay').html(data.totalKgPay);
                        $('.totalPay').html(data.debt);
                        $('.currency').formatCurrency();
                    }
                }
            });


            return false;
        });
    },
    //hang ky gui
    saveCodeShipper: function () {
        $('body').on('click', '.save-code-shipper', function (e) {
            e.preventDefault();
            var $this = $(this);
            var mvd = $this.data('code');
            $parent = $(this).closest('tr');
            var kg = parseFloat($parent.find('.kg-' + mvd).val());
            if (kg <= 0) {
                $('#myModal').modal('show').find('.modal-title').html('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-warning"></i></label> Bạn chưa nhập cân nặng.</div>');
                setTimeout(function () {
                    $('#myModal').modal('hide');
                }, 2000);
                return false;
            }

            var cusId = $this.data('cid');
            $parent.find('.barcode-' + mvd).iCheck('check');
            var long = parseFloat($parent.find('.long-' + mvd).val());
            var wide = parseFloat($parent.find('.wide-' + mvd).val());
            var high = parseFloat($parent.find('.high-' + mvd).val());
            var note = $parent.find('.note-' + mvd).val();
            var kgChange = parseFloat((long * wide * high) / 6000); //can nang quy doi
            kgChange = kgChange.toFixed(2);
            var kgPay = (kgChange > kg ? kgChange : kg);

            $parent.find('.kgPay-' + mvd).attr('kgPay', kgPay);
            $parent.find('.barcode-' + mvd).attr('kgPay', kgPay);

            $.ajax({
                url: '/save-ship-code',
                type: 'post',
                data: {
                    kg: kg, mvd: mvd, long: long, wide: wide, high: high, cusId: cusId,
                    kgChange: kgChange, kgPay: kgPay, note: note
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').html('Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                success: function (rs) {
                    $('.modal-container').html('<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-check-circle"></i></label>  Cập nhật thành công.</div>');
                    setTimeout(function () {
                        $('#myModal').modal('hide');
                    }, 1000);
                    if (rs.data) {
                        var data = rs.data;
                        $('.numCodeCheck').html(data.numCode);
                        $('.totalKgPay').html(data.totalKg);
                        $('.totalPay').html(data.totalPrice);
                        //set lai tien kg
                        $('#feekg-' + mvd).html(data.feekg);
                    }
                }
            });


            return false;

        });
    },
    //del-code
    removeBarcode: function () {
        $('body').on('click', '.btn-delete-code', function (e) {
            var $this = $(this);
            $this.prop('disabled', true);
            $parent = $this.closest('.rows');

            var mvd = $this.data('code');
            var oid = $this.data('oid');
            var sid = $this.data('sid');
            var kgPay = parseFloat($('.kgPay-' + mvd).attr('kgPay'));
            var num_row = parseInt($('#order-' + oid).attr('numcode'));


            $.ajax({
                url: '/transfercode/delete-code',
                type: 'post',
                data: {
                    mvd: mvd, oid: oid, sid: sid
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                success: function (rs) {
                    $this.prop('disabled', false);
                    $('#myModal').modal('hide');

                    if (rs.data) {
                        var data = rs.data;
                        $('.totalKgPay').html(data.totalKgPay);
                        $('.totalPay').html(data.debt);
                        $('.currency').formatCurrency();
                        $('.numCodeCheck').html(data.numCode);
                        if (data.totalKg) {
                            $('.kgPay-' + mvd).attr('kgPay', kgPay);
                            $parent.find('.totalkgFee').html(data.totalKg);
                            $parent.find('.weightPrice').html(data.weightPrice);
                            $parent.find('.totalPayment').html(data.totalPayment);
                            $parent.find('.totalPaid').html(data.totalPaid);
                            $parent.find('.debtAmount').html(data.debtAmount);
                        }
                    }
                    if (num_row == 1) {
                        $('#order-' + oid).remove();
                    }
                    num_row = (num_row > 1) ? --num_row : 1;
                    $('#order-' + oid).attr('numcode', num_row);
                    $this.closest('tr').fadeOut(function () {
                        this.remove();
                    });
                }
            });

            return false;
        });
    },
    //del-code-ship
    removeCodeShip: function () {
        $('body').on('click', '.del-code-ship', function (e) {
            var $this = $(this);
            $this.prop('disabled', true);

            var mvd = $this.data('code');
            var cusId = $this.data('cid');

            $.ajax({
                url: '/del-code-ship',
                type: 'post',
                data: {
                    mvd: mvd, cusId: cusId
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#myModal').modal('show').find('.modal-title').html('<i class="icon fa fa-warning red-color"></i> Thông báo');
                    $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                },
                success: function (rs) {
                    $this.prop('disabled', false);
                    if (rs.data) {
                        var data = rs.data;
                        $('.numCodeCheck').html(data.numCode);
                        $('.totalKgPay').html(data.totalKg);
                        $('.totalPay').html(data.totalPrice);
                        $this.closest('tr').fadeOut(function () {
                            this.remove();
                        });

                        $('.modal-container').html('<div style="text-align: center"><label class="font24 red-color"><i class="fa fa-check-circle"></i></label>  Xóa thành công.</div>');
                        setTimeout(function () {
                            $('#myModal').modal('hide');
                        }, 1000);
                    }
                }
            });

            return false;
        });
    },
    //send sms
    sendMessage: function () {
        $('.send-sms').on('click', function (e) {
            $('.modal-container').html('');
            if (confirm('Bạn có chắc chắn muốn gửi thông báo cho đơn hàng này không?')) {
                $identify = $(this).closest('tr').children('td').find('.identify a>b').text();
                $('h3.modal-title').html('Gửi thông báo cho đơn hàng: <strong> ' + $identify + ' </strong>');
                e.preventDefault();
                $('#myModal').modal('show');
                $.ajax({
                    url: '/gui-thong-bao-' + $(this).data('id'),
                    type: 'get',
                    beforeSend: function () {
                        $('.modal-container').html('<div style="text-align: center;padding-top: 150px"><img src="/images/loader.gif"/></div>');
                    },
                    success: function (model) {
                        $('.modal-container').html(model);
                    }
                });

            }
            return false;
        });
    },
    readURL: function (input, id) {
        var $ = jQuery;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('[data-id="' + id + '"]').children('img').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    },
};


(function ($) {


    $.fn.autoSubmit = function (options) {
        return $.each(this, function () {
            // VARIABLES: Input-specific
            var input = $(this);
            var column = input.attr('name');

            // VARIABLES: Form-specific
            var form = input.parents('form');
            var method = form.attr('method');
            var action = form.attr('action');

            // VARIABLES: Where to update in database
            var where_val = form.find('#where').val();
            var where_col = form.find('#where').attr('name');

            // ONBLUR: Dynamic value send through Ajax
            input.bind('blur', function (event) {
                // Get latest value
                var value = input.val();
                // AJAX: Send values
                $.ajax({
                    url: action,
                    type: method,
                    data: {
                        val: value,
                        col: column,
                        w_col: where_col,
                        w_val: where_val
                    },
                    cache: false,
                    timeout: 10000,
                    success: function (data) {
                        // Alert if update failed
                        if (data) {
                            alert(data);
                        }
                        // Load output into a P
                        else {
                            $('#notice').text('Updated');
                            $('#notice').fadeOut().fadeIn();
                        }
                    }
                });
                // Prevent normal submission of form
                return false;
            })
        });
    }
})(jQuery);


