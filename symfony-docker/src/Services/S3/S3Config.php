<?php
declare(strict_types=1);


namespace App\Services\S3;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class S3Config
{
    public function __construct(
        #[Autowire(env: 'S3_KEY')]
        public string $credentialKey,
        #[Autowire(env: 'S3_SECRET')]
        public string $credentialSecret,
        #[Autowire(env: 'S3_REGION')]
        public string $region,
        #[Autowire(env: 'S3_VERSION')]
        public string $version,
        #[Autowire(env: 'S3_ENDPOINT')]
        public string $endpoint,
        #[Autowire(env: 'bool:S3_USE_PATH_STYLE_ENDPOINT')]
        public bool $usePathStyleEndpoint,
    )
    {
    }
}
