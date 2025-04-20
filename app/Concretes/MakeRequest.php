<?php


namespace App\Concretes;


// use App\Abstracts\IMakeRequest;
use App\Traits\ErrorTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MakeRequest
{
    use ErrorTrait;
    /**
     * @var Client
     */
    private $client;

    public function __construct($base_url = "")
    {
        //dd($base_url);
        $this->client = new Client([
            'base_uri' => $base_url ?? env("API_URL"),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function getAsJson(string $url, array $headers = [])
    {

        try {

            $result = $this->client->get($url, [
                "headers" => $headers
            ]);

            $body = json_decode($result->getBody()->getContents());

            return (object)[
                "body" => $body,
                "status_code" => 200
            ];
        } catch (ClientException $ex) {
            return $this->formulateErrorResponse($ex);
        } catch (\Exception $ex) {
            return $this->formulateErrorResponse($ex);
        }
    }

    public function postAsJson(string $url, array $body = [], array $headers = [])
    {
        try {
            $result = $this->client->post($url, [
                'json' => $body,
                "headers" => $headers
            ]);

            $body = json_decode($result->getBody()->getContents());
            return (object)[
                "body" => $body,
                "status_code" => 200,
                "status" => true
            ];
        } catch (ClientException $ex) {
            return $this->formulateErrorResponse($ex);
        } catch (\Exception $ex) {
            return $this->formulateErrorResponse($ex);
        }
    }

    public function postAsForm(string $url, array $body = [], array $headers = [])
    {

        try {
            $result = $this->client->post($url, [
                'multipart' => $body,
                "headers" => $headers
            ]);

            $body = json_decode($result->getBody()->getContents());
            return (object)[
                "body" => $body,
                "status_code" => 200
            ];
        } catch (ClientException $ex) {
            return $this->formulateErrorResponse($ex);
        } catch (\Exception $ex) {
            return $this->formulateErrorResponse($ex);
        }
    }
}
