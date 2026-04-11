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

            $adapter = new AzureBlobStorageAdapter(
                $client,
                $config['container'],
                $config['prefix'] ?? ''
            );

            $filesystem = new Filesystem($adapter, $config);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
