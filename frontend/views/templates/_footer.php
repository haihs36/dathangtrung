<?php $setting = Yii::$app->controller->setting; ?>
<footer class="page-foot section-inset-4 bg-dark">
    <section class="footer-content">
        <div class="container">
            <div class="row text-left clearleft-custom">
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <div class="rd-navbar-brand"><a class="brand-name" href="index.html"><span class="icon fa fa-fw fa-truck"></span><span> ĐẶT HÀNG TRUNG</span></a></div>
                    <p>Liên hệ với chúng tôi. Chúng tôi luôn sẵn sàng giúp đỡ bạn.</p>
                    <address>
                        <dl>
                            <dt>Trụ sở chính:</dt>
                            <dd><?php echo $setting['address']; ?></dd>
                        </dl>
                        <dl class="dl-horizontal-mod-1">
                            <dt>Phone</dt>
                            <dd><a class="text-primary" href="callto:<?php echo $setting['hotline']; ?>"><?php echo $setting['hotline']; ?></dd>
                        </dl>
                        <!--<dl class="dl-horizontal-mod-1">
                            <dt>Fax</dt>
                            <dd><a class="text-primary" href="callto:#">(91) 11 4752 1433</a></dd>
                        </dl>-->
                        <dl class="dl-horizontal-mod-1">
                            <dt>Email</dt>
                            <dd><a class="text-primary" href="mailto:<?php echo $setting['email']; ?>"><?php echo $setting['email']; ?></a></dd>
                        </dl>
                    </address>
                </div>
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <h4>Chuyên mục</h4>
                    <ul class="list-marked well6">
                        <li><a href="/"><i class="fa fa-angle-right" aria-hidden="true"></i> Home</a></li>
                        <li><a href="/bang-gia"><i class="fa fa-angle-right" aria-hidden="true"></i> Bảng giá</a></li>
                        <li><a href="/huong-dan"><i class="fa fa-angle-right" aria-hidden="true"></i> Hướng dẫn</a></li>
                        <li><a href="/tin-tuc"><i class="fa fa-angle-right" aria-hidden="true"></i> Tin tức</a></li>
                        <li><a href="/chinh-sach"><i class="fa fa-angle-right" aria-hidden="true"></i> Chính sách</a></li>
                        <li><a href="/tuyen-dung"><i class="fa fa-angle-right" aria-hidden="true"></i> Tuyển dụng</a></li>
                    </ul>
                </div>
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <h4>Facebook</h4>
                    <iframe src="https://www.facebook.com/plugins/page.php?href=&tabs=timeline&width=340&height=250&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=false&appId=" width="340" height="250" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>
                </div>
            </div>
        </div>
    </section>
    <section class="copyright bg-darkest well5">
        <div class="container">
            <p class="pull-sm-left">© <span id="copyright-year">2018</span> design by <a href="https://thietkewebos.com/kho-giao-dien.html" target="_blank" title="Công ty thiế kế website chuyên nghiệp"><strong>OS Media</strong></a></p>
            <ul class="list-inline pull-sm-right offset-3">
                <li><a class="fab fa-facebook-f" href="#"></a></li>
                <li><a class="fab fa-twitter" href="#"></a></li>
                <li><a class="fab fa-pinterest" href="#"></a></li>
                <li><a class="fab fa-vimeo-v" href="#"></a></li>
                <li><a class="fab fa-google" href="#"></a></li>
                <li><a class="fas fa-rss" href="#"></a></li>
            </ul>
        </div>
    </section>
    <!-- {%FOOTER_LINK}-->
</footer>

<a href="javascript:;" class="scroll-top-link" id="scroll-top">
    <i class="fa fa-angle-up"></i>
</a>


<script>
    jQuery(document).ready(function () {
        $('#header .hd-main .main-right .search .btn').on('click', function (e) {
            e.stopPropagation();
            $('#header .search-box').show();
        });
        $(document).on('click', function (e) {
            if ($('#header .search-box').is(":visible")) {
                if (!$('#header .search-box').is(e.target) && $('#header .search-box').has(e.target).length === 0) {
                    $('#header .search-box').hide();
                }
            }

        })
    });
</script>
