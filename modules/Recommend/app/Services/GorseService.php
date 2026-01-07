<?php

namespace Modules\Recommend\Services;

use Illuminate\Support\Facades\Http;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Classes\GorseUser;
use Modules\Recommend\Classes\RowAffected;

class GorseService
{
    private string $endpoint;

    private ?string $apiKey;

    public function __construct()
    {
        $this->endpoint = config('recommend.endpoint');
        $this->apiKey   = config('recommend.apiKey');
    }

    /**
     * Insert a user to Gorse.
     */
    public function insertUser(GorseUser $user): ?RowAffected
    {
        $response = $this->request('POST', '/api/user', $user);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Update user from Gorse.
     */
    public function updateUser(GorseUser $user): ?RowAffected
    {
        $path = '/api/user/' . rawurlencode($user->getUserId());

        $response = $this->request('PATCH', $path, $user);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Get user from Gorse.
     */
    public function getUser(string $userId): ?GorseUser
    {
        $path = '/api/user/' . rawurlencode($userId);

        $response = $this->request('GET', $path, null);

        return $response ? GorseUser::fromJSON($response) : null;
    }

    /**
     * Delete user from Gorse.
     */
    public function deleteUser(string $userId): ?RowAffected
    {
        $path = '/api/user/' . rawurlencode($userId);

        $response = $this->request('DELETE', $path, null);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Insert item to Gorse.
     */
    public function insertItem(GorseItem $item): ?RowAffected
    {
        $response = $this->request('POST', '/api/item', $item);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Update item from Gorse.
     */
    public function updateItem(GorseItem $item): ?RowAffected
    {
        $path = '/api/item/' . rawurlencode($item->getItemId());

        $response = $this->request('PATCH', $path, $item);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Get item from Gorse.
     */
    public function getItem(string $itemId): ?GorseItem
    {
        $path = '/api/item/' . rawurlencode($itemId);

        $response = $this->request('GET', $path, null);

        return $response ? GorseItem::fromJSON($response) : null;
    }

    /**
     * Delete item from Gorse.
     */
    public function deleteItem(string $itemId): ?RowAffected
    {
        $path = '/api/item/' . rawurlencode($itemId);

        $response = $this->request('DELETE', $path, null);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Insert category to item.
     */
    public function insertItemCategory(string $itemId, string $categoryId): ?RowAffected
    {
        $path = sprintf(
            '/api/item/%s/category/%s',
            rawurlencode($itemId),
            rawurlencode($categoryId)
        );

        $response = $this->request('PUT', $path, null);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Delete category from item.
     */
    public function deleteItemCategory(string $itemId, string $categoryId): ?RowAffected
    {
        $path = sprintf(
            '/api/item/%s/category/%s',
            rawurlencode($itemId),
            rawurlencode($categoryId)
        );

        $response = $this->request('DELETE', $path, null);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Inert feedback to Gorse.
     */
    public function insertFeedback(GorseFeedback $feedback): ?RowAffected
    {
        $response = $this->request('POST', '/api/feedback', [$feedback]);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Delete feedback from Gorse.
     */
    public function deleteFeedback(string $type, string $userId, string $itemId): ?RowAffected
    {
        $path = sprintf(
            '/api/feedback/%s/%s/%s',
            rawurlencode($type),
            rawurlencode($userId),
            rawurlencode($itemId)
        );

        $response = $this->request('DELETE', $path, null);

        return $response ? RowAffected::fromJSON($response) : null;
    }

    /**
     * Get personalized recommendations for a user.
     *
     * @return array<int, string>|null
     */
    public function getRecommend(string $userId, int $n, int $offset): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $path = sprintf('/api/recommend/%s?%s', rawurlencode($userId), http_build_query($query));

        return $this->request('GET', $path, null);
    }

    /**
     * Get personalized recommendations for a user filtered by categories.
     *
     * @param array<int, string> $categories
     *
     * @return array<int, string>|null
     */
    public function getRecommendByCategory(string $userId, int $n, int $offset, array $categories): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $queryString = http_build_query($query);
        foreach ($categories as $category) {
            $queryString .= '&category=' . rawurlencode($category);
        }

        $path = sprintf('/api/recommend/%s?%s', rawurlencode($userId), $queryString);

        return $this->request('GET', $path, null);
    }

    /**
     * Get non-personalized recommendations.
     *
     * @return array<int, string>|null
     */
    public function getNonPersonalizedRecommend(string $name, int $n, int $offset): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $path = sprintf('/api/non-personalized/%s?%s', rawurlencode($name), http_build_query($query));

        return $this->request('GET', $path, null);
    }

    /**
     * Get non-personalized recommendations filtered by categories.
     *
     * @param array<int, string> $categories
     *
     * @return array<int, string>|null
     */
    public function getNonPersonalizedRecommendByCategory(string $name, int $n, int $offset, array $categories): ?array
    {
        $query = [
            'n'      => $n,
            'offset' => $offset,
        ];

        $queryString = http_build_query($query);
        foreach ($categories as $category) {
            $queryString .= '&category=' . rawurlencode($category);
        }

        $path = sprintf('/api/non-personalized/%s?%s', rawurlencode($name), $queryString);

        return $this->request('GET', $path, null);
    }

    /**
     * Send HTTP request to Gorse API.
     *
     * @return mixed|null
     */
    private function request(string $method, string $uri, mixed $body): mixed
    {
        try {
            $http = Http::baseUrl($this->endpoint);

            if ($this->apiKey) {
                $http->withHeaders(['X-API-Key' => $this->apiKey]);
            }

            $response = match (strtoupper($method)) {
                'GET'    => $http->get($uri),
                'POST'   => $http->post($uri, $body instanceof \JsonSerializable ? $body->jsonSerialize() : $body),
                'PUT'    => $http->put($uri, $body instanceof \JsonSerializable ? $body->jsonSerialize() : $body),
                'PATCH'  => $http->patch($uri, $body instanceof \JsonSerializable ? $body->jsonSerialize() : $body),
                'DELETE' => $http->delete($uri, $body instanceof \JsonSerializable ? $body->jsonSerialize() : $body),
                default  => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            if ($response->successful()) {
                return $response->json();
            }

            logger()->error('Gorse API Error: ' . $response->body());

            return null;
        } catch (\Exception $e) {
            logger()->error('Gorse API Error', ['e' => $e]);

            return null;
        }
    }
}
