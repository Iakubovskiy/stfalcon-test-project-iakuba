<?php
declare(strict_types=1);


namespace App\Services\S3;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class S3Service
{
    private S3Client $s3Client;


    public function __construct(
        private S3Config $config,
        #[Autowire(env: 'S3_BUCKET')]
        private string $bucket,
        #[Autowire(env: 'DOMAIN')]
        private string $domain,
        #[Autowire(env: 'PROTOCOL')]
        private string $protocol,
    )
    {
        $parameters = [
            'credentials' => new Credentials($this->config->credentialKey, $this->config->credentialSecret),
            'region' => $this->config->region,
            'version' => $this->config->version,
            'use_path_style_endpoint' => $this->config->usePathStyleEndpoint,
        ];
        if (!empty($this->config->endpoint)) {
            $parameters['endpoint'] = $this->config->endpoint;
        }

        $this->s3Client = new S3Client($parameters);
    }

    private function generateFileUrl(string $key):string
    {
        return $this->protocol.$this->bucket.$this->domain.$key;
    }

    public function uploadFileByBody(string $key, string $body): string
    {

        $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body
        ]);
        return $this->generateFileUrl($key);
    }

    public function getFile( string $key): ?string
    {
        $result = $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);

        return $result['Body'] ?? null;
    }

    public function saveFileArray(array $files): array
    {
        $urls = [];
        foreach ($files as $file) {
            $key = $file->getClientOriginalName();
            if (!$file->isValid()) {
                return [];
            }
            if (!$file->getPathname() || !file_exists($file->getPathname())) {
                return [];
            }

            $fileUrl = $this->uploadFileByBody($key, file_get_contents($file->getPathname()));
            $urls[] = $fileUrl;
        }
        return $urls;
    }
}
