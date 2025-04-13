<aside class="sidebar sidebar_right  alpha units" role="complementary" itemscope="itemscope">
    <div class="inner_sidebar extralight-border">
        <section id="custom_html-9" class="widget_text widget clearfix widget_custom_html">
            <div class="textwidget custom-html-widget">
                <a href="/images/hotline-van-chuyen-khoi-nguyen.png/dat-hang/" class="aligncenter" style="float: none; text-align: center; margin: 0px; padding: 0px;">
                    <img class="aligncenter size-full wp-image-3586 lazyloaded" src="/images/dat-hang-trung-quoc-300x250.png" data-lazy-src="/images/dat-hang-trung-quoc-300x250.png" alt="" width="276" height="230" data-was-processed="true">
                    <span class="image-overlay overlay-type-extern" style="left: -5px; top: 0px; overflow: hidden; display: block; height: 250px; width: 286px;"><span class="image-overlay-inside"></span></span>
                </a>
                <?php if($submenu){
                    foreach ($submenu as $val){ if($val['status'] ==0) continue;
                    ?>
                        <br> <a href="<?php echo !empty($val['redirect']) ? $val['redirect'] : 'javascript:void(0)' ?>">
                        <strong> » <?php echo $val['title'] ?></strong>
                    </a>
                <?php } } ?>
            </div>
        </section>
        <section id="custom_html-5" class="widget_text widget clearfix widget_custom_html">
            <div class="textwidget custom-html-widget">
                <strong>
                    <a href="javascript:void(0)" class="aligncenter" style="float: none; text-align: center; margin: 0px; padding: 0px;">
                        <img src="/images/hotline-van-chuyen-khoi-nguyen-1.png" data-lazy-src="/images/hotline-van-chuyen-khoi-nguyen-1.png" alt="hotline-van-chuyen-khoi-nguyen" width="276" height="87" class="aligncenter size-full wp-image-3587 lazyloaded" data-was-processed="true">
                        <span class="image-overlay overlay-type-extern">
                            <span class="image-overlay-inside"></span>
                        </span>
                    </a>
                </strong>
            </div>
        </section>
        <?php if($articleNewMost){ ?>
        <section id="newsbox-2" class="widget clearfix newsbox">
            <h3 class="widgettitle">
                <strong><a href="javascript:void(0)">Bài viết mới</a></strong>
            </h3>
            <ul class="news-wrap image_size_widget">
                <?php  foreach ($articleNewMost as $item){
                    $urlDetail = \yii\helpers\Url::to(['articles/detail', 'slug' => $item['slug']]);
                    $thumb = \common\components\CommonLib::getAvatar($item['thumb'])
                    ?>
                <li class="news-content post-format-standard">
                    <a class="news-link" title="<?php echo $item['title'] ?>" href="<?= $urlDetail ?>">
                        <span class="news-thumb ">
                            <img width="36" height="36" src="<?= $thumb ?>" data-lazy-src="<?= $thumb ?>" class="attachment-widget size-widget wp-post-image lazyloaded">
                        </span>
                        <strong class="news-headline">
                            <?php echo \common\components\CommonLib::cut_string($item['title'],50);  ?>
                            <span class="news-time">
                                <?= \common\components\CommonLib::sw_get_current_weekday(date('Y-m-d H:i:s',$item['time'])) ?>
                            </span>
                        </strong>
                    </a>
                </li>
                <?php } ?>
            </ul>

        </section>
        <?php } ?>
    </div>
</aside>