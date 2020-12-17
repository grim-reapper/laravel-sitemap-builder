<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php
if(config('sitemap.xslt_file_name')){
    echo '<?xml-stylesheet type="text/xsl" href="'.url(config('sitemap.xslt_file_name')).'"?>';
}
?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"
              xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php /** @var  \GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap $sitemap */ ?>
    @foreach($sitemaps as $name => $sitemap)
    <sitemap>
        @unless (empty($name))
        <loc>{{ $sitemap->getPath() }}</loc>
        @endunless

        <?php
            /** @var  \GrimReapper\LaravelSitemap\Contracts\Entities\Url $latest */
            $latest = $sitemap->getUrls()->last(function (\GrimReapper\LaravelSitemap\Contracts\Entities\Url $url) {
                return $url->getLastMod();
            });
        ?>

        @unless (is_null($latest))
        <lastmod>{{ $latest->getLastMod()->format(DateTime::ATOM) }}</lastmod>
        @endunless
    </sitemap>
    @endforeach
</sitemapindex>
