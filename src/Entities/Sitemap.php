<?php


namespace GrimReapper\LaravelSitemap\Entities;
use GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap as SitemapContract;
use GrimReapper\LaravelSitemap\Contracts\Entities\Url as UrlContract;
use Illuminate\Support\Collection;

class Sitemap implements SitemapContract
{

    protected $path;

    protected $urls;

    public function __construct()
    {
        $this->urls = new Collection();
    }

    /**
     * Set the sitemap path.
     *
     * @param string $path
     *
     * @return SitemapContract
     */
    public function setPath(string $path): SitemapContract
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the sitemap path.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Get the sitemap's URLs.
     *
     * @return Collection
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    /**
     * Set the URLs Collection.
     *
     * @param Collection $urls
     *
     * @return SitemapContract
     */
    public function setUrls(Collection $urls): SitemapContract
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * Make a sitemap instance.
     *
     * @return SitemapContract
     */
    public static function make(): SitemapContract
    {
        return new static();
    }

    /**
     * Get a URL instance by its loc.
     *
     * @param string $loc
     * @param mixed|null $default
     *
     * @return \GrimReapper\LaravelSitemap\Entities\Url|null
     */
    public function getUrl(string $loc, $default = null)
    {
        return $this->getUrls()->get($loc, $default);
    }

    /**
     * Add a sitemap URL to the collection.
     *
     * @param UrlContract $url
     *
     * @return SitemapContract
     */
    public function add(UrlContract $url): SitemapContract
    {
        $this->urls->put($url->getLoc(), $url);
        return $this;
    }

    /**
     * Add many urls to the collection.
     *
     * @param iterable|mixed $urls
     *
     * @return SitemapContract
     */
    public function addMany(iterable $urls): SitemapContract
    {
        foreach ($urls as $url) {
            $this->add($url);
        }

        return $this;
    }

    /**
     * Create and Add a sitemap URL to the collection.
     *
     * @param string $loc
     * @param callable $callback
     *
     */
    public function create(string $loc, callable $callback)
    {
        return $this->add(tap(Url::make($loc), $callback));
    }

    /**
     * Check if the url exists in the sitemap items.
     *
     * @param string $url
     *
     * @return bool
     */
    public function has(string $url): bool
    {
        return $this->urls->has($url);
    }

    /**
     * Check if the number of URLs is exceeded.
     *
     * @return bool
     */
    public function isExceeded(): bool
    {
        return $this->count() > $this->getMaxSize();
    }

    /**
     * Chunk the sitemap to multiple chunks if the size is exceeded.
     *
     * @return Collection
     */
    public function chunk(): Collection
    {
        return $this->urls
            ->chunk($this->getMaxSize())
            ->mapWithKeys(function ($item, $index) {
                $pathInfo = pathinfo($this->getPath());
                $index    = $index + 1;
                $path     = $pathInfo['dirname'].'/'.$pathInfo['filename'].'-'.$index.'.'.$pathInfo['extension'];

                return [
                    $index => (new Sitemap)->setPath($path)->setUrls($item),
                ];
            });
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getUrls()->values()->toArray();
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count(): int
    {
        return $this->urls->count();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the max size.
     *
     * @return int
     */
    protected function getMaxSize(): int
    {
        return (int) config('sitemap.urls-max-size', 50000);
    }
}
