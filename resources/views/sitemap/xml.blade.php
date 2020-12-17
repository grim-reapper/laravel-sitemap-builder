<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php
if(config('sitemap.xslt_file_name')){
    echo '<?xml-stylesheet type="text/xsl" href="'.url(config('sitemap.xslt_file_name')).'"?>';
}
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <?php
        /**
         * @var  \GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap  $sitemap
         * @var  \GrimReapper\LaravelSitemap\Contracts\Entities\Url      $url
         */
    ?>
    @foreach($sitemap->getUrls() as $url)
        <url>
            @if ($url->has('loc'))
            <loc>{{ $url->get('loc') }}</loc>
            @endif

            @if ($url->has('lastmod'))
            <lastmod>{{ $url->formatLastMod() }}</lastmod>
            @endif

            @if ($url->has('changefreq'))
            <changefreq>{{ $url->get('changefreq') }}</changefreq>
            @endif

            @if ($url->has('priority'))
            <priority>{{ $url->get('priority') }}</priority>
            @endif
        </url>
    @endforeach
</urlset>
