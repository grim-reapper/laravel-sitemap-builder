<?php
/**
 * @var  \GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap  $sitemap
 * @var  \GrimReapper\LaravelSitemap\Contracts\Entities\Url      $url
 */
?>
@foreach($sitemap->getUrls() as $url)
{{ $url->getLoc() }}<br>
@endforeach
