<?php

namespace App\Traits;

use Http;
use App\Exceptions\RedirectException;
use Illuminate\Http\Client\Pool;

trait ClientHttpTrait
{
    // private $baseUrl = config('application.api_url');

    public $baseUrl;

    public function initializeBaseUrl()
    {
        $this->baseUrl = config('application.api_url');
    }

    public function get($url, $params = [])
    {
        return $this->clientRequest('get', $url, ['query' => $params]);
    }

    public function post($url, $params = [], $body = [])
    {
        return $this->clientRequest('post', $url, ['query' => $params, 'body' => $body]);
    }

    public function put($url, $params = [], $body = [])
    {
        return $this->clientRequest('put', $url, ['query' => $params, 'body' => $body]);
    }

    public function delete($url, $params = [])
    {
        return $this->clientRequest('delete', $url, ['query' => $params]);
    }

    private function clientRequest($method, $url, $options = [])
    {
        $this->initializeBaseUrl();
        $url = $this->baseUrl . $url;

        $query = $options['query'] ?? [];
        $body = $options['body'] ?? [];
        try {
            $request = Http::withoutVerifying()->withToken($this->token);

            // Add query parameters to the URL
            if (!empty($query)) {
                $url .= '?' . http_build_query($query);
            }
            file_put_contents(storage_path('/logs/test.txt'), $url . "\n", FILE_APPEND);

            if (in_array($method, haystack: ['post', 'put', 'patch'])) {
                $response = $request->$method($url, $body); // Send body for POST/PUT requests
            } else {
                $response = $request->$method($url);
            }

            if ($response->status() == 401) {
                auth('client')->logout();
                return redirect()->route('client.login')->with('error', 'Session expired. Please login again.');
            }

            if ($response->status() == 500) {
                throw new RedirectException('Internal server error');
            }
            if ($response->status() == 404) {
                throw new RedirectException('Resource not found');
            }
            if ($response->status() == 403) {
                throw new RedirectException('Forbidden');
            }

            // if($response->status() == 422) {
            //     throw new RedirectException('Unprocessable Entity');
            // }


            return $response;
        } catch (\Exception $e) {
            throw new RedirectException('Unable to process request: ' . $e->getMessage());
        }
    }


    public function makeRequestsInPool(array $requests)
    {
        $this->initializeBaseUrl();

        return Http::pool(fn(Pool $pool) => collect($requests)->mapWithKeys(function ($request) use ($pool) {
            $method = $request['method'];
            $url = $request['url'];
            $params = $request['params'] ?? [];
            $body = $request['body'] ?? [];
            $name = $request['name'];
            $token = $this->token; // Assuming the token is stored as $this->token

            // Add query parameters to the URL for GET requests
            if ($method === 'get' && !empty($params)) {
                $url .= '?' . http_build_query($params);
            }

            // Prepare options for the request
            $options = $method === 'get'
                ? []
                : ['json' => $body, 'query' => $params]; // POST/PUT/etc. include params and body


            file_put_contents(storage_path('/logs/test.txt'), $url . "\n", FILE_APPEND);

            // Use the token for authorization
            return [
                $name => $pool->as($name)->withToken($this->token)->$method($this->baseUrl . $url, $options),
            ];
        }));

    }


}
