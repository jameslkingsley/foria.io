<?php

namespace App\Support;

class VideoDestinations
{
    /**
     * The destination key.
     *
     * @var string
     */
    protected $key;

    /**
     * The path prefix string.
     *
     * @var string
     */
    protected $pathPrefix;

    /**
     * All available destination paths.
     *
     * @var object
     */
    protected $paths;

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct(string $key, $data = null)
    {
        $this->key = $key;

        $this->paths = $data
            ? (object) $data
            : (object) [
                'key' => $this->key,
                'directory' => "videos/{$this->key}",
                'unprocessed' => "videos/{$this->key}/unprocessed.",
                'processed' => "videos/{$this->key}/processed.mp4",
                'preview' => "videos/{$this->key}/preview.mp4",
                'thumbnails' => "videos/{$this->key}/thumbnails"
            ];
    }

    /**
     * Makes a new object from the given data.
     *
     * @return App\Support\VideoDestinations
     */
    public static function make($data)
    {
        return new static(
            $data->key,
            $data
        );
    }

    /**
     * Gets the raw paths object.
     *
     * @return object
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Sets the prefix for the paths.
     *
     * @return App\Support\VideoDestinations
     */
    public function prefix(string $uri)
    {
        $this->pathPrefix = str_finish($uri, '/');

        return $this;
    }

    /**
     * Gets the given destination path.
     *
     * @return string
     */
    public function __get($name)
    {
        $path = $this->pathPrefix
            ? $this->pathPrefix . $this->paths->{$name}
            : $this->paths->{$name};

        $this->pathPrefix = null;

        return $path;
    }
}
