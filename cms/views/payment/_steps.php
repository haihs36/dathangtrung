<div style="margin: 18px 0">
    <div class="stepwizard">
        <div class="stepwizard-row">
            <div class="stepwizard-step">
                <button type="button" class="btn <?php echo (($action == 'index') ? 'btn-primary': 'btn-default') ?> btn-circle">1</button>
                <p>Trả hàng</p>
            </div>
            <div class="stepwizard-step">
                <button disabled type="button" class="btn <?php echo (($action == 'complete' || $action == 'print') ? 'btn-primary': 'btn-default') ?> btn-circle">2</button>
                <p>Hoàn thành</p>
            </div>
        </div>
    </div>
</div>