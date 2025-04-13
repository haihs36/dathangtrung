<div class="banner">
    <div class="banner__top">
        <?= \frontend\widgets\Slider::widget() ?>
    </div>
    <div class="banner_bot">
        <div class="all">
            <div class="wrap">
                <div class="txt-tool">
                            <span style="font-family: Arial; font-size: 20px;">
                                CÀI ĐẶT CÔNG CỤ ĐẶT HÀNG CHO TRÌNH DUYỆT NGAY <i style="color: #f9a828"
                                                                                 class="far fa-hand-point-right"></i>
                            </span>
                </div>
                <div class="button">
                    <?php

                        $extension = 'https://chromewebstore.google.com/detail/c%C3%B4ng-c%E1%BB%A5-%C4%91%E1%BA%B7t-h%C3%A0ng-dathangt/okkkpohnnjcpmdmbikolalomknfadkpn?authuser=2&hl=vi';
                     ?>
                    <a href="<?= $extension ?>" target="_blank" class="btn btn-2"><i class="fab fa-chrome"></i><span
                                class="text">Chrome</span></a>
                    <a href="<?= $extension ?>" target="_blank" class="btn btn-3"><i class="fab fa-chrome"></i><span
                                class="text">Cốc cốc</span></a>

                </div>
            </div>
        </div>
    </div>

</div>
<div class="quytrinhdathang sec-pad">
    <div class="all">
        <div class="sec-title fz-36">
            <h1>Quy trình đặt hàng</h1>
            <span class="border"></span>
        </div>
        <ul class="list-step">
            <li class="item">
                <a>
                    <div class="img"><i class="fas fa-clipboard-list"></i></div>
                    <div class="text semibold"><span class="color">1.</span> Đăng ký tài khoản</div>
                </a>
            </li>
            <li class="item">
                <a>
                    <div class="img"><i class="fas fa-cog"></i></div>
                    <div class="text semibold">
                        <span class="color">2.</span> Cài đặt công cụ mua hàng
                    </div>
                </a>
            </li>
            <li class="item">
                <a>
                    <div class="img"><i class="fas fa-cart-plus"></i></div>
                    <div class="text semibold"><span class="color">3.</span> Chọn hàng và thêm hàng vào giỏ</div>
                </a>
            </li>
            <li class="item">
                <a>
                    <div class="img"><i class="fas fa-share-square"></i></div>
                    <div class="text semibold"><span class="color">4.</span> Gửi đơn đặt hàng</div>
                </a>
            </li>
            <li class="item">
                <a>
                    <div class="img"><i class="fas fa-credit-card"></i></div>
                    <div class="text semibold"><span class="color">5.</span> Đặt cọc tiền hàng</div>
                </a>
            </li>
            <li class="item">
                <a>
                    <div class="img"><i class="fas fa-box-open"></i></div>
                    <div class="text semibold"><span class="color">6.</span>Nhận hàng và thanh toán</div>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="chinhsach sec-pad">
    <div class="all">
        <div class="sec-title fz-36">
            <h1>Chính sách</h1>
            <span class="border"></span>
        </div>
        <div class="wrap">
            <div class="child hover-zoomin">
                <div class="p-pad img icon">
                    <img src="/home/img/chinhsach-icon-1.png" alt="">
                </div>
                <h4 class="p-pad fz-18 color2">Chính sách kiểm hàng</h4>
                <div class="text">
                    <p>Khách hàng có thể lựa chọn hoặc không lựa chọn sử dụng dịch vụ kiểm hàng</p>
                </div>
            </div>
            <div class="child hover-zoomin">
                <div class="img p-pad icon">
                    <img src="/home/img/chinhsach-icon-2.png" alt="">
                </div>
                <h4 class="fz-18 p-pad color2">Chính sách khiếu nại</h4>
                <div class="text">
                    <p> Xử lý mọi khiếu nại nhanh chóng, Bồi hoàn 100% giá trị đơn hàng nếu lỗi phát sinh  lỗi từ Nhập hàng Trung Quốc 247</p>
                </div>
            </div>
            <div class="child hover-zoomin">
                <div class="img p-pad icon">
                    <img src="/home/img/chinhsach-icon-3.png" alt="">
                </div>
                <h4 class="fz-18 p-pad color2">Hướng dẫn tổng hợp</h4>
                <div class="text">
                    <p> Hướng dẫn chi tiết, có nhân viên hỗ trợ 1:1  tìm hàng và nguồn hàng đảm bảo nhanh chóng, thuận tiện, mọi lúc mọi nơi</p>
                </div>
            </div>
            <div class="child hover-zoomin">
                <div class="img p-pad icon">
                    <img src="/home/img/chinhsach-icon-4.png" alt="">
                </div>
                <h4 class="fz-18 p-pad color2">Thông tin thanh toán</h4>
                <div class="text">
                    <p>Rõ ràng, minh bạch, tiện lợi qua ví điện tử, khách hàng có thể theo dõi được dòng tiền của mình qua điện thoại và máy tính</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="brand sec-pad">
    <div class="all">
        <div class="wrap">

            <div class="child">
                <div class="img">
                    <img src="/home/img/tmall-brand.png" alt="">
                </div>
                <div class="text">
                    <p>
                        Tmall trang web cung cấp nguồn hàng khồng lồ dành cho khách bán buôn cần nhập hàng Trung Quốc giá sỉ. Nguồn hàng ở Tmall rất đa dạng, bạn có thể tìm thấy ở đây từ hàng thời trang đến hàng gia dụng, hàng điện tử, linh kiện, phụ kiện điện thoại, hàng nội thất...
                    </p>
                </div>
            </div>
            <div class="child">
                <div class="img">
                    <img src="/home/img/1688-brand.png" alt="">
                </div>
                <div class="text">
                    <p>
                        Dathangtrung.vn là cách gọi khác của trang Alibaba.cn, trang Alibaba dành riêng cho thị trường nội địa Trung Quốc.  Dathangtrung.vn là website cung cấp cho người mua và người bán nhiều hình thức buôn bán khác nhau, không chỉ dưới dạng B2B (hình thức giao dịch giữa doanh nghiệp với doanh nghiệp) mà còn cả B2C (giao dịch giữa doanh nghiệp với khách hàng).
                    </p>
                </div>
            </div>
            <div class="child">
                <div class="img">
                    <img src="/home/img/taobao-brand.png" alt="">
                </div>
                <div class="text">
                    <p>
                        TaoBao là một hệ thống Website của tập đoàn Alibaba bán hàng dạng thương mại điện tử,
                        mọi quá trình mua và bán đều thông qua mang Internet kể cả thanh toán và chọn hàng.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="chungtoicamket sec-pad">
    <div class="all">
        <div class="sec-title fz-36">
            <h1>Chúng tôi cam kết</h1>
            <span class="border"></span>
        </div>
        <div class="wrap">
            <div class="img">
                <img src="/home/img/chungtoicamket-bg.png" alt="">
            </div>
            <div class="content">
                <div class="child hover-zoomin">
                    <h4 class="fz-18">KHÔNG CÓ THỜI GIAN TRỄ KHI ĐẶT HÀNG</h4>
                    <div class="text">
                        <p>Quý khách chủ động với toàn bộ quy trình nạp tiền, thanh toán và đặt hàng tự động</p>
                    </div>
                </div>
                <div class="child hover-zoomin">
                    <h4 class="fz-18">Cam kết mua hàng trong 24h</h4>
                    <div class="text">
                        <p>Nhập Hàng Trung Quốc 247 sau khi nhận được đơn đặt hàng, chúng tôi cam kết mua hàng nhanh chóng trong vòng 24h</p>
                    </div>
                </div>
                <div class="child hover-zoomin">
                    <h4 class="fz-18">Tiết kiệm thời gian quản lý</h4>
                    <div class="text">
                        <p> Toàn bộ quy trình được tự động hóa, Tình trạng đơn hàng được cập nhật liên tục và tự động qua hệ thống website thông minh</p>
                    </div>
                </div>
                <div class="child hover-zoomin">
                    <h4 class="fz-18">Hổ trợ trực tuyến 24/7</h4>
                    <div class="text">
                        <p>Khách hàng hoàn toàn yên tâm với kinh nghiệm 10 năm đặt hàng và đội ngũ cskh tận tình,  tiếp nhận và hỗ trợ  nhanh chóng những vướng mắc của khách hàng.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="quyenloi sec-pad2">
    <div class="all">
        <div class="sec-title fz-36">
            <h1>Quyền lợi khách hàng</h1>
            <span class="border"></span>
        </div>
        <div class="wrap">
            <div class="child hover-zoomin">
                <div class="p-pad img icon">
                    <img src="/home/img/quyenloi-icon-1.png" alt="">
                </div>
                <h4 class="p-pad fz-18 color2">Ưu đãi theo<br>
                    cấp độ thành viên</h4>
                <div class="text">
                    <p>Chính sách chiết khấu, giảm giá cho Quý hàng có nhu cầu nhập hàng thường xuyên.</p>
                </div>
            </div>
            <div class="child hover-zoomin">
                <div class="p-pad img icon">
                    <img src="/home/img/quyenloi-icon-2.png" alt="">
                </div>
                <h4 class="p-pad fz-18 color2">Ưu đãi theo<br>
                    doanh thu tháng</h4>
                <div class="text">
                    <p>Với những khách hàng có doanh thu/ đơn hàng lớn, được miễn phí 100% phí dịch vụ đặt hàng</p>
                </div>
            </div>
            <div class="child hover-zoomin">
                <div class="p-pad img icon">
                    <img src="/home/img/quyenloi-icon-3.png" alt="">
                </div>
                <h4 class="p-pad fz-18 color2">Ưu đãi theo<br>
                    sản lượng tháng</h4>
                <div class="text">
                    <p>Có nhân viên kiểm hàng hỗ trợ nếu sản lượng đặt khách hàng lớn và mức độ thường xuyên</p>
                </div>
            </div>
        </div>
    </div>

</div>


