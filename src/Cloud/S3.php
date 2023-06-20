<?php

namespace Windsor\Cloud;

use Windsor\Core\Utils\Env;
use Aws\S3\S3Client;

class S3
{

    private string $bucket;

    private S3Client $s3;

    public function __construct()
    {

        $region = Env::get("Windsor_AWS_S3_REGION");
        $key = Env::get("Windsor_AWS_S3_KEY");
        $secret = Env::get("Windsor_AWS_S3_SECRET");
        $this->bucket = Env::get("Windsor_AWS_S3_BUCKET");

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret,
            ],
        ]);

    }

    public function put(string $key, string $content) {
        $this->s3->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $content
        ]);
    }

    public function get(string $key) {
       $res = $this->s3->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);
       return $res['Body']->getContents();
    }

}