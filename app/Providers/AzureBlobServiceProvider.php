<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

/**
 * 🔧 AzureBlobServiceProvider
 *
 * Registers the 'azure' filesystem driver using League\Flysystem v3
 * Azure Blob Storage adapter. Required for media uploads to Azure.
 *
 * Also registers a custom URL generator since the Azure adapter
 * is not natively recognized by Laravel's FilesystemAdapter.
 */
class AzureBlobServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('azure', function (Application $app, array $config) {
            $connectionString = sprintf(
                'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s;EndpointSuffix=core.windows.net',
                $config['name'],
                $config['key']
            );

            $client = BlobRestProxy::createBlobService($connectionString);

            // Build base URL for public access
            $baseUrl = $config['url'] ?? sprintf(
                'https://%s.blob.core.windows.net/%s',
                $config['name'],
                $config['container']
            );

            // Use a wrapper adapter that supports getUrl()
            $adapter = new AzureBlobStorageAdapterWithUrl(
                $client,
                $config['container'],
                $config['prefix'] ?? '',
                $baseUrl
            );

            $filesystem = new Filesystem($adapter, $config);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}

/**
 * Extends AzureBlobStorageAdapter to add getUrl() support.
 * Laravel's FilesystemAdapter checks method_exists($adapter, 'getUrl')
 * to resolve URLs — the base adapter doesn't have this method.
 */
class AzureBlobStorageAdapterWithUrl extends AzureBlobStorageAdapter
{
    protected string $baseUrl;

    public function __construct(
        $client,
        string $container,
        string $prefix = '',
        string $baseUrl = ''
    ) {
        parent::__construct($client, $container, $prefix);
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Get the public URL for a file path.
     * Called by Laravel's FilesystemAdapter->url()
     */
    public function getUrl(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
