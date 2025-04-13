<?php
    use yii\helpers\Url;

    $setting = Yii::$app->controller->setting;
    $baseUrl = Yii::$app->params['baseUrl'];
    /*seo*/
    $title_seo                  = !empty($data->seoText->title) ? $data->seoText->title : $data->title;
    $keyword_seo                = !empty($data->seoText->keywords) ? $data->seoText->keywords : $title_seo;
    $description_seo            = !empty($data->seoText->description) ? $data->seoText->description : $data->short;
    $currentUrl                 = Url::to(['articles/detail', 'slug' => $data->slug, 'id' => $data->news_id], true);
    $data_seo['title']          = $title_seo;
    $data_seo['keywords']       = $keyword_seo;
    $data_seo['description']    = $description_seo;
    $data_seo['datePublished']  = !empty($data->publishtime) ? date("c", $data->publishtime) : date("c", $data->time);
    $data_seo['dateModified']   = date("c", $data->time);
    $data_seo['og:url']         = ['itemprop' => "url", 'content' => $currentUrl];
    $data_seo['og:title']       = ['itemprop' => "headline", 'content' => !empty($data->title) ? $data->title : $title_seo];
    $data_seo['og:description'] = ['itemprop' => "description", 'content' => !empty($data->description) ? $data->description : $description_seo];;
    $data_seo['og:site_name'] = $setting['site_name'];
    $data_seo['og:type']      = 'article';
    $og_image                 = !empty($data['thumb']) ? $data['thumb'] : $data['image'];
    if (!empty($og_image)) {
        $data_seo['og:image']        = ['itemprop' => 'thumbnailUrl', 'content' => Yii::$app->params['FileDomain'] . $og_image];
        $data_seo['og:image:width']  = 490;
        $data_seo['og:image:height'] = 294;
    }
    $data_seo['canonical'] = ['href' => $currentUrl];
    \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);
    $current_cat = isset($data->category[0]) ? $data->category[0]: null;
?>

<section class="page-title-section">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-header-wrap">
                    <div class="page-header">
                        <h2 class="txt-header"><?php echo $data->title ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Single-Service-Start -->
<section class="page-content section-70 section-md-98">
    <div class="container">
        <div class="row">
            <div class=" col-xs-12">
                <div class="view-content">
                        <h3><?php echo $data->short ?></h3>
                        <div class="body">
                            <?php echo $data->bodyText->text ?>
                        </div>
                    <?php if($ortherNews){ ?>
                        <div class="dich-vu-lien-quan item-block block section-70 section-md-98">
                            <h2>Tin liên quan</h2>
                            <hr class="divider divider-lg bg-primary hr-lg-left-0">
                            <ul>
                                <?php foreach ($ortherNews as $item){
                                    $urlDetail = Url::to(['articles/detail', 'slug' => $item['slug'], 'id' => $item['news_id']]);
                                    ?>
                                    <li>
                                        <a href="<?php echo $urlDetail ?>" title="<?php echo $item['title'] ?>"><?php echo $item['title'] ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
<!-- Single-Service-End-->