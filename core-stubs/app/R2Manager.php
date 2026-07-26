<?php
/**
 * ArtiFrame Core Engine
 *
 * @package     ArtiFrame
 * @author      Artilingo
 * @license     AGPLv3 (Attribution-ShareAlike Required)
 * @link        https://artiframe.artilingo.com
 *
 * NOTICE: This file is part of the ArtiFrame ecosystem.
 * Any derivative works or patches MUST retain this original copyright notice
 * and remain open-source under the AGPLv3 license.
 */

namespace App;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class R2Manager
{
    private S3Client $client;
    private string $bucketName;

    public function __construct()
    {
        $accountId = $_ENV['R2_ACCOUNT_ID'] ?? '';
        $accessKey = $_ENV['R2_ACCESS_KEY'] ?? '';
        $secretKey = $_ENV['R2_SECRET_KEY'] ?? '';
        $this->bucketName = $_ENV['R2_BUCKET_NAME'] ?? '';

        $this->client = new S3Client([
            'version' => 'latest',
            'region'  => 'auto',
            'endpoint' => "https://{$accountId}.r2.cloudflarestorage.com",
            'credentials' => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
            // R2 için gerekli olabiliyor
            'use_path_style_endpoint' => true,
        ]);
    }

    /**
     * R2 bucket'a dosya yükler.
     *
     * @param string $sourcePath Yerel dosya yolu (veya tmp_name)
     * @param string $destinationKey Hedef R2 path/isim
     * @param string $contentType MIME tipi
     * @return string|false Yükleme başarılıysa dosyanın R2 adresi döner, aksi halde false.
     */
    public function upload(string $sourcePath, string $destinationKey, string $contentType = 'application/octet-stream')
    {
        try {
            $result = $this->client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $destinationKey,
                'SourceFile' => $sourcePath,
                'ContentType' => $contentType,
                // Public okuma yetkisi vermek gerekirse ACL eklenebilir, ancak R2 genelde bucket policy kullanır.
                // 'ACL'    => 'public-read',
            ]);

            // Cloudflare public dev.url veya bucket url döndürür. Projeye göre public URL yapılandırılır.
            // Örnek public bucket path (Eğer R2'ye özel domain bağlıysa onu dönebilirsiniz)
            // https://pub-xxxxxx.r2.dev/destinationKey gibi
            return $destinationKey; 
        } catch (AwsException $e) {
            error_log("R2 Yükleme Hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Base64 string'i bucket'a kaydeder.
     *
     * @param string $base64String
     * @param string $destinationKey
     * @param string $contentType
     * @return string|false
     */
    public function uploadBase64(string $base64String, string $destinationKey, string $contentType = 'image/jpeg')
    {
        // Data URI şeması varsa temizle (data:image/png;base64,...)
        if (strpos($base64String, ',') !== false) {
            $parts = explode(',', $base64String);
            $base64String = $parts[1];
        }

        $decodedData = base64_decode($base64String);
        if ($decodedData === false) {
            return false;
        }

        try {
            $result = $this->client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $destinationKey,
                'Body'   => $decodedData,
                'ContentType' => $contentType,
            ]);

            return $destinationKey;
        } catch (AwsException $e) {
            error_log("R2 Yükleme Hatası: " . $e->getMessage());
            return false;
        }
    }

    /**
     * R2 bucket'tan dosya siler.
     *
     * @param string $key Silinecek dosyanın yolu/anahtarı (örn: avatars/123.jpg)
     * @return bool Başarılıysa true, değilse false
     */
    public function delete(string $key)
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key'    => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            error_log("R2 Silme Hatası: " . $e->getMessage());
            return false;
        }
    }
}
