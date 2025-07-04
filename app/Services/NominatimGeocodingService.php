<?php
namespace App\Services;

class NominatimGeocodingService
{
    protected string $email;
    
    public function __construct(string $email = '')
    {
        $this->email = $email;
    }

    public function reverse(float $lat, float $lon): ?object
    {
        $params = http_build_query([
            'format' => 'json',
            'addressdetails' => 1,
            'lat' => $lat,
            'lon' => $lon,
            'email' => $this->email
        ]);

        $url = "https://nominatim.openstreetmap.org/reverse?$params";

        $client = new \GuzzleHttp\Client(['headers' => [
            'User-Agent' => 'MyApp/1.0 (https://devsih3.sultengprov.gi.id)'
        ]]);

        try {
            $res = $client->get($url);
            if ($res->getStatusCode() !== 200) return null;

            return json_decode((string) $res->getBody()); // return object
        } catch (\Exception $e) {
            return null;
        }
    }


}
