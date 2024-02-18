<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IPFSService
{
  protected $baseUrl = 'https://api.pinata.cloud/';
  protected $token;

  public function __construct()
  {
    $this->token = env('PINATA_JWT');
  }

  public function pinFile($file)
  {
    $fileNameWithExtension = $file->getClientOriginalName();
    $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);
    $fileContents = file_get_contents($file->getRealPath());

    $url = $this->baseUrl . 'pinning/pinFileToIPFS';
    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->attach(
      'file',
      $fileContents,
      $fileName
    )->post($url, [
      'pinataMetadata' => json_encode(['name' => $fileName]),
      'pinataOptions' => json_encode(['cidVersion' => 1]),
    ]);

    return $response->successful() ? $response->json() : false;
  }

  public function updateMetadata($hash, $metadata)
  {
    $url = $this->baseUrl . 'pinning/hashMetadata';

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->put($url, [
      'ipfsPinHash' => $hash,
      'name' => $metadata['name'],
    ]);

    return $response->successful() ? $response->json() : false;
  }

  public function unpinFile($hash)
  {
    $url = $this->baseUrl . 'pinning/unpin/' . $hash;

    $response = Http::withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->delete($url);

    return $response->successful() ? $response->json() : false;
  }
}
