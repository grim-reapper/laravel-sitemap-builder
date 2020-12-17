<?php

namespace GrimReapper\LaravelSitemap\Contracts;

use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use GrimReapper\LaravelSitemap\Contracts\Entities\Sitemap;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
interface SitemapManagerContract extends Arrayable, Jsonable, \Countable, \JsonSerializable
{
    /**
     * Set the format.
     *
     * @param string $format
     *
     * @return $this
     */
    public function format(string $format): SitemapManagerContract;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Create and add a sitemap to the collection.
     *
     * @param  string    $name
     * @param  callable  $callback
     *
     * @return $this
     */
    public function create(string $name, callable $callback): SitemapManagerContract;

    /**
     * Add a sitemap to the collection.
     *
     * @param  string                                                $name
     * @param  Sitemap  $sitemap
     *
     * @return $this
     */
    public function add(string $name, Sitemap $sitemap): SitemapManagerContract;

    /**
     * Get the sitemaps collection.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Get a sitemap instance.
     *
     * @param  string      $name
     * @param  mixed|null  $default
     *
     * @return \GrimReapper\LaravelSitemap\Entities\Sitemap|mixed|null
     */
    public function get(string $name, $default = null);

    /**
     * Check if a sitemap exists.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Remove a sitemap from the collection by key.
     *
     * @param  string|array  $names
     *
     * @return $this
     */
    public function forget($names): SitemapManagerContract;

    /**
     * Render the sitemaps.
     *
     * @param  string|null  $name
     *
     * @return string|null
     */
    public function render(string $name = null): ?string;

    /**
     * Save the sitemaps.
     *
     * @param string $path
     * @param string|null $name
     * @param bool $backup_file
     * @return $this
     */
    public function save(string $path, string $name = null, bool $backup_file = true): SitemapManagerContract;

    /**
     * Render the Http response.
     *
     * @param string|null $name
     * @param int $status
     * @param array $headers
     *
     */
    public function respond(string $name = null, int $status = Response::HTTP_OK, array $headers = []);
}
