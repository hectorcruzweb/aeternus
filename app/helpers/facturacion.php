<?php

function getCertificateData($certificatePath)
{
    $certificateCAcer = $certificatePath;
    $certificateCAcerContent = file_get_contents($certificateCAcer);
    $certificateCAcerContent = str_replace(array('\n', '\r'), '', $certificateCAcerContent);
    $certificateCApemContent =  '-----BEGIN CERTIFICATE-----' . PHP_EOL . chunk_split(base64_encode($certificateCAcerContent), 64, PHP_EOL) . '-----END CERTIFICATE-----' . PHP_EOL;
    $certificateCApem = $certificateCAcer . '.pem';

    file_put_contents($certificateCApem, $certificateCApemContent);
    $data = openssl_x509_parse(file_get_contents($certificateCApem));
    return $data;
}