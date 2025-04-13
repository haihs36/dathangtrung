jQuery(document).ready(function ($) {
    //format date
    var startDate = $('#startDate');
    var endDate = $('#endDate');
    if (startDate.length) {
        startDate.datepicker({
            format: 'dd/mm/yyyy'
        });
    }
    if(endDate.length){
        endDate.datepicker({
            format: 'dd/mm/yyyy'
        });
    }
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
    }
};
/*delete*/
$('body').on("click", ".delete_btn",function(event){
    event.preventDefault();
    var Ids = $('.grid-view').yiiGridView('getSelectedRows');
    if(Ids.length > 0){
        if(confirm("Bạn có chắc chắn muốn xóa các bản ghi đã chọn không?")){
            $.ajax({
                type: 'POST',
                url :  $(this).data('url'),
                data : {ids: Ids},
                dataType : 'JSON',
                success : function(response) {
                    if (response.status == 'success' && response.recordDeleted.length) {
                        $.each(response.recordDeleted,function (u,v) {
                            $('#rowId' + v).fadeOut();
                        });
                        notify.success('Xóa thành công');
                        
                    } else {
                        notify.success('Xóa thất bại');
                    }
                }
            })
        }
    }else{
        alert('Vui lòng chọn bản ghi cần xóa');
    }
});

$('.ableToVerifyEmail').click(function () {
    if (confirm('Are you sure ?')) {
        var id = $(this).attr('id');
        var recordId = id.split('ableToVerifyEmail')[1];
        $.ajax({
            url: baseUrl + 'user/verify-email?id=' + recordId,
            type: "POST",
            dataType: 'json',
            beforeSend: function () {
                $('.loading-img').show();
            },
            success: function (response) {
                if (response.status == 'success') {
                    if (response.recordEmailVerified == 1) {
                        $('#' + id).fadeOut();
                        $('#email_verified_td' + recordId).html('Yes');
                    }
                } else {
                    alert('Status not updated successfully');
                }
            },
            complete: function () {
                $('.loading-img').hide();
            },
            error: function () {
                alert('There was a problem while requesting to verify email. Please try again');
            }
        });
    }
});

$('.ableToChangeStatus').click(function () {
    if (confirm('Are you sure ?')) {
        var id = $(this).attr('id');
        var url = $(this).attr('url');
        var recordId = id.split('ableToChangeStatus')[1];
        $.ajax({
            url: url,
            type: "POST",
            data: 'id=' + recordId,
            dataType: 'json',
            beforeSend: function () {
                $('.loading-img').show();
            },
            success: function (response) {
                if (response.status == 'success') {
                    if (response.recordStatus == 1) {
                        $('#' + id + ' span').attr('class', 'glyphicon glyphicon-ok');
                        $('#' + id).attr('title', 'Khóa');
                        $('#status_td' + recordId).html('Kích hoạt');
                        $('#rowId' + recordId).attr('class', 'success');
                    } else {
                        $('#' + id + ' span').attr('class', 'glyphicon glyphicon-ban-circle');
                        $('#status_td' + recordId).html('Khóa');
                        $('#rowId' + recordId).attr('class', 'danger');
                        $('#' + id).attr('title', 'Kích hoạt');
                    }
                    notify.success('Updated status successfully');
                } else {
                    notify.success('Status not updated successfully');
                }
            },
            complete: function () {
                $('.loading-img').hide();
            },
            error: function () {
                notify.success('There was a problem while requesting to change status. Please try again');
            }
        });
    }
});

$('.ableToDelete').click(function () {
    if (confirm('Ban có chắc chắn muốn xóa không?')) {
        var id = $(this).attr('id');
        var url = $(this).attr('url');
        var recordId = id.split('ableToDelete')[1];
         var button = $(this).addClass('disabled');
        $.ajax({
            url: url,
            type: "POST",
            data: 'id=' + recordId,
            dataType: 'json',
            beforeSend: function () {
                $('.loading-img').show();
            },
            success: function (response) {
                if (response.status == 'success' && response.recordDeleted == 1) {
                    notify.success('Xóa thành công');

                    button.closest('tr').fadeOut(function () {
                        this.remove();
                    });
                   //$('#rowId' + recordId).fadeOut();
                } else {
                    notify.success('Xóa thất bại');
                }
            },
            complete: function () {
                $('.loading-img').hide();
            },
            error: function () {
                alert('There was a problem while requesting to delete the record. Please try again');
            }
        });
    }
});

$('.ableToDeleteRole').click(function () {
    if (confirm('Are you sure ?')) {
        deleteRole($(this));
    }
});
function deleteRole(obj, confirmed) {
    var id = obj.attr('id');
    var url = obj.attr('url');
    var recordId = id.split('ableToDeleteRole')[1];
    var data = confirmed ? 'id=' + recordId + '&confirmed=1' : 'id=' + recordId;
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: 'json',
        beforeSend: function () {
            $('.loading-img').show();
        },
        success: function (response) {
            if (response.status == 'blocked') {
                alert(response.message);
            } else if (response.status == 'staged') {
                if (response.childOrParent == false) {
                    alert('This Role does not any child or parent.');
                } else if (response.childOrParent == true) {
                    if (confirm('This Role has ' + response.children + ' child role(s) and ' + response.parent + ' parent role(s).\n Do you really want to proceed?')) {
                        deleteRole(obj, true);
                    }
                }
            } else if (response.status == 'success' && response.recordDeleted == 1) {
                $('#rowId' + recordId).fadeOut();
            } else {
                alert('Deletion not successful');
            }
        },
        complete: function () {
            $('.loading-img').hide();
        },
        error: function () {
            alert('There was a problem while requesting to delete the record. Please try again');
        }
    });
}

$("#userRoleParent").change(function () {
    var id = $(this).val();
    var url = $(this).attr('url');
    $.ajax({
        url: url,
        type: "POST",
        data: 'id=' + id,
        //dataType:'json',
        beforeSend:function(){   $('.loading-img').show();  },
        success: function (response) {
            $("#userRoleChild").html(response);
            var url = $("#userRoleChild").attr('url');
            var allControllerMode = $("#allControllerMode").val();
            var controller = $("#allControllerFilter").val();
            $.ajax({
                url: url,
                type: "POST",
                data: 'id=' + id + '&controllerMode=' + allControllerMode + '&controller=' + controller,
                //dataType:'json',
                beforeSend: function () {
                    $('#window_progress').show();
                },
                success: function (response) {
                    $("#permissionSectionBody").html(response);
                },
                complete: function () {
                    $('#window_progress').hide();
                },
                error: function () {
                    alert('There was a problem while requesting to delete the record. Please try again');
                }
            });
        },
        complete: function () {
            //$('.loading-img').hide();
        },
        error: function () {
            alert('There was a problem while requesting to delete the record. Please try again');
        }
    });
});

$("#userRoleChild").click(function () {
    var parentId = $("#userRoleParent").val();
    var childId = $(this).val();
    var controller = $("#allControllerFilter").val();
    var allControllerMode = $("#allControllerMode").val();
    var url = $(this).attr('url');
    $.ajax({
        url: url,
        type: "POST",
        data: 'id=' + parentId + '&child=' + childId + '&controllerMode=' + allControllerMode + '&controller=' + controller,
        //dataType:'json',
        beforeSend: function () {
            $('#window_progress').show();
        },
        success: function (response) {
            $("#permissionSectionBody").html(response);
        },
        complete: function () {
            $('#window_progress').hide();
        },
        error: function () {
            alert('There was a problem while requesting to delete the record. Please try again');
        }
    });
});

$("#allControllerMode").change(function () {
    var id = $(this).val();
    var controller = $("#allControllerFilter").val();
    var childRole = $("#userRoleChild").val();
    var parentId = $("#userRoleParent").val();
    var url = $("#userRoleChild").attr('url');
    $.ajax({
        url: url,
        type: "POST",
        data: 'controllerMode=' + id + '&controller=' + controller + '&child=' + childRole + '&id=' + parentId,
        //dataType:'json',
        beforeSend: function () {
            $('#window_progress').show();
        },
        success: function (response) {
            $("#permissionSectionBody").html(response);
        },
        complete: function () {
            $('#window_progress').hide();
        },
        error: function () {
            alert('There was a problem while requesting to delete the record. Please try again');
        }
    });
});

$("#allControllerFilter").change(function () {
    var id = $(this).val();
    var mode = $("#allControllerMode").val();
    var childRole = $("#userRoleChild").val();
    var parentId = $("#userRoleParent").val();
    var url = $("#userRoleChild").attr('url');
    $.ajax({
        url: url,
        type: "POST",
        data: 'controller=' + id + '&controllerMode=' + mode + '&child=' + childRole + '&id=' + parentId,
        //dataType:'json',
        beforeSend: function () {
            $('#window_progress').show();
        },
        success: function (response) {
            $("#permissionSectionBody").html(response);
        },
        complete: function () {
            $('#window_progress').hide();
        },
        error: function () {
            alert('There was a problem while requesting to delete the record. Please try again');
        }
    });
});

$('.ableToLogoutUser').click(function () {
    if (confirm('This user will be logged out from the moment. Are you sure ?')) {
        var id = $(this).attr('id');
        var url = $(this).attr('url');
        var recordId = id.split('ableToLogoutUser')[1];
        var rowId = recordId.split('.').join("");
        $.ajax({
            url: url,
            type: "POST",
            data: 'ip=' + recordId,
            dataType: 'json',
            beforeSend: function () {
                $('.loading-img').show();
            },
            success: function (response) {
                if (response.status == 'success') {
                    if (response.recordLoggedout == 1) {
                        $('#rowId' + rowId).fadeOut();
                    }
                } else {
                    alert('User has been logged out successfully');
                }
            },
            complete: function () {
                $('.loading-img').hide();
            },
            error: function () {
                alert('There was a problem while logging out the user. Please try again');
            }
        });
    }
});



$('#select_all').click(function () {
    ($(this).prop('checked') == true) ? $('.select_me').prop('checked', true) : $('.select_me').prop('checked', false);
});













