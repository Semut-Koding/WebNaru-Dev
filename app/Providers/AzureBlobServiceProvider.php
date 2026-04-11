<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\AzureBlobStorage\AzureBlobStorageAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\Resources;

/**
 * 🔧 AzureBlobServiceProvider
 *
 * Registers the 'azure' filesystem driver using League\Flysystem v3
 * Azure Blob Storage adapter. Required for media uploads to Azure.
 *
 * Generates SAS (Shared Access Signature) URLs for private containers
 * since direct public access is not permitted on this storage account.
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

            // Build base URL for SAS URL generation
            $baseUrl = $config['url'] ?? sprintf(
                'https://%s.blob.core.windows.net',
                $config['name']
            );

            // Use wrapper adapter that supports getUrl() with SAS tokens
            $adapter = new AzureBlobStorageAdapterWithSas(
                $client,
                $config['container'],
                $config['prefix'] ?? '',
                $baseUrl,
                $config['name'],
                $config['key'],
            );

            $filesystem = new Filesystem($adapter, $config);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}

/**
 * Extends AzureBlobStorageAdapter to add getUrl() with SAS token support.
 *
 * Azure Blob private containers require a Shared Access Signature (SAS)
 * token appended to the URL for read access. This adapter generates
 * time-limited (60 min) signed URLs automatically.
 */
class AzureBlobStorageAdapterWithSas extends AzureBlobStorageAdapter
{
    protected string $baseUrl;
    protected string $accountName;
    protected string $accountKey;
    protected string $containerName;

    public function __construct(
        $client,
        string $container,
        string $prefix,
        string $baseUrl,
        string $accountName,
        string $accountKey,
    ) {
        parent::__construct($client, $container, $prefix);
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->accountName = $accountName;
        $this->accountKey = $accountKey;
        $this->containerName = $container;
    }

    /**
     * Get a signed (SAS) URL for a file path — valid for 60 minutes.
     * Called by Laravel's FilesystemAdapter->url()
     */
    public function getUrl(string $path): string
    {
        $sasHelper = new BlobSharedAccessSignatureHelper(
            $this->accountName,
            $this->accountKey
        );

        $expiry = (new \DateTime())->modify('+60 minutes');

        $sas = $sasHelper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            "{$this->containerName}/" . ltrim($path, '/'),
            'r',                                       // read-only
            $expiry->format('Y-m-d\TH:i:s\Z'),
            '',                                        // start (empty = now)
            '',                                        // ip
            'https'                                    // protocol
        );

        return "{$this->baseUrl}/{$this->containerName}/" . ltrim($path, '/') . "?{$sas}";
    }
}
