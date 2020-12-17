<?php


namespace GrimReapper\LaravelSitemap\Supports;


use GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap;
use GrimReapper\LaravelSitemap\Contracts\SitemapManagerContract;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SitemapManager implements SitemapManagerContract
{

    protected $sitemaps;

    protected $format = 'xml';

    public function __construct()
    {
        $this->sitemaps = new Collection();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->all()->toArray();
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
        return $this->sitemaps->count();
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
     * Set the format.
     *
     * @param string $format
     *
     * @return SitemapManagerContract
     */
    public function format(string $format): SitemapManagerContract
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Create and add a sitemap to the collection.
     *
     * @param string $name
     * @param callable $callback
     *
     * @return SitemapManagerContract
     */
    public function create(string $name, callable $callback): SitemapManagerContract
    {
        return $this->add($name, tap(\GrimReapper\LaravelSitemap\Entities\Sitemap::make()->setPath($name), $callback));
    }

    /**
     * Add a sitemap to the collection.
     *
     * @param string $name
     * @param Sitemap $sitemap
     *
     * @return SitemapManagerContract
     */
    public function add(string $name, Sitemap $sitemap): SitemapManagerContract
    {
        $this->sitemaps->put($name, $sitemap);

        return $this;
    }

    /**
     * Get the sitemaps collection.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->sitemaps;
    }

    /**
     * Get a sitemap instance.
     *
     * @param string $name
     * @param mixed|null $default
     *
     * @return \GrimReapper\LaravelSitemap\Entities\Sitemap|mixed|null
     */
    public function get(string $name, $default = null)
    {
        return $this->sitemaps->get($name, $default);
    }

    /**
     * Check if a sitemap exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        if ( ! Str::contains($name, '.'))
            return $this->sitemaps->has($name);

        list($name, $key) = explode('.', $name, 2);

        $map = $this->sitemaps->filter(function (Sitemap $map) {
            return $map->isExceeded();
        })->get($name);

        return is_null($map)
            ? false
            : $map->chunk()->has(intval($key));
    }

    /**
     * Remove a sitemap from the collection by key.
     *
     * @param string|array $names
     *
     * @return SitemapManagerContract
     */
    public function forget($names): SitemapManagerContract
    {
        $this->sitemaps->forget($names);

        return $this;
    }

    /**
     * Render the sitemaps.
     *
     * @param string|null $name
     *
     * @return string|null
     */
    public function render(string $name = null): ?string
    {
        return SitemapBuilder::make()->build($name, $this->sitemaps, $this->format);
    }

    /**
     * Save the sitemaps.
     *
     * @param string $path
     * @param string|null $name
     * @param bool $backup_file
     * @return SitemapManagerContract
     * @throws \Throwable
     */
    public function save(string $path, string $name = null, bool $backup_file = true): SitemapManagerContract
    {
        if ($this->sitemaps->isEmpty())
            return $this;

        if($backup_file && file_exists($path)){
            $file_info = pathinfo($path);
            $dirname = data_get($file_info,'dirname');
            $filename = data_get($file_info,'filename');
            $extension = data_get($file_info,'extension');
            $backup_file_name = $dirname.DIRECTORY_SEPARATOR.$filename.'_old'.'.'.$extension;
            rename($path, $backup_file_name);
        }
        file_put_contents($path, $this->render($name));

        foreach ($this->sitemaps as $key => $sitemap) {
            if ($sitemap->isExceeded())
                $this->saveMultiple($path, $sitemap);
        }

        return $this;
    }

    /**
     * Render the Http response.
     *
     * @param string|null $name
     * @param int $status
     * @param array $headers
     *
     */
    public function respond(string $name = null, int $status = Response::HTTP_OK, array $headers = [])
    {
        return response($this->render($name), $status, array_merge($this->getResponseHeaders(), $headers));
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
     * Save multiple sitemap.
     *
     * @param  string                                                $path
     * @param  \GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap  $sitemap
     *
     * @throws \Throwable
     */
    private function saveMultiple(string $path, Sitemap $sitemap)
    {
        $pathInfo = pathinfo($path);
        $chunks   = $sitemap->chunk();

        foreach ($chunks as $key => $item) {
            file_put_contents(
                $pathInfo['dirname'].DIRECTORY_SEPARATOR.$pathInfo['filename'].'-'.$key.'.'.$pathInfo['extension'],
                SitemapBuilder::make()->build((string) $key, $chunks, $this->format)
            );
        }
    }

    /**
     * Get the response header.
     *
     * @return array
     */
    protected function getResponseHeaders(): array
    {
        return Arr::get([
            'xml' => ['Content-Type' => 'application/xml'],
            'rss' => ['Content-Type' => 'application/rss+xml'],
            'txt' => ['Content-Type' => 'text/plain'],
        ], $this->format, []);
    }
}
