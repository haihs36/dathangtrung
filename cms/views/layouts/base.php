<?php
\cms\assets\AppAsset::register($this);
$homeUrl = Yii::$app->params['baseUrl'];
$baseUrl = Yii::$app->params['adminUrl'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?= \yii\helpers\Html::encode($this->title) ?></title>
    <?php $this->head() ?>
      <script>
          var homeUrl = '<?php echo $homeUrl ?>';
          var baseUrl = '<?php echo $baseUrl ?>';
      </script>
      <script src="/js/jQuery-2.1.4.min.js"></script>
      <script src="/bootstrap/js/bootstrap.js"></script>
      <script src="/js/main.js"></script>
  </head>
  <body class="skin-blue sidebar-mini wysihtml5-supported">
  <?php $this->beginBody() ?>
     <?= $content ?>
  <?php $this->endBody() ?>
  <!-- modal -->
  <div class="modal " id="myModal" style="display: none;">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close btn-close" data-dismiss="modal" aria-hidden="true">×</button>
                  <h3 class="modal-title" id="exampleModalLabel">Thông báo</h3>
              </div>
              <div class="modal-body">
                  <div class="modal-container"></div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-right btn-close" data-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>

  <div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title" id="exampleModalLabel">Thông báo</h4>
              </div>
              <div class="modal-body">
                  <form>
                      <div class="form-group">
                          <p> Bạn có muốn <label for="status-name" class="control-label"></label> "<label for="name-cate" class="control-label"></label>" này không ? </p>
                      </div>
                  </form>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-default pull-left" id="dismiss-modal">Hủy</button>
                  <button type="button" class="btn btn-primary" id="action-confirm-change-status" data-opt="8" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Đang xử lý">Đồng ý</button>
              </div>
          </div>
      </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
        $("#myModal").modal({
            show: false,
            backdrop: 'static'
        });
        $('.btn-close').on('click',function(){
            $('input[id=orderNumber]').focus();  
        });     

/*
        $.ajax({
              url: 'https://thietkewebos.com/thong-bao.html',
              type: 'get',             
              dataType: "json", 
              success: function (rs) {
                  if (rs.status) {
                      $('#myModal').modal('show');
                      $('.modal-container').html(rs.view);
                  } 

              }
          });*/

    });




  </script>
  </body>
</html>
<?php $this->endPage() ?>
