<?php

namespace Nevadskiy\Tokens\Repository;

use Illuminate\Database\Eloquent\Model;
use Nevadskiy\Tokens\Exceptions\TokenNotFoundException;
use Nevadskiy\Tokens\TokenEntity;

class TokenRepository
{
    /**
     * Get a token entity by a token string and token name.
     *
     * @param string $token
     * @param string $name
     * @return TokenEntity
     * @throws TokenNotFoundException
     */
    public function getByTokenAndName(string $token, string $name): TokenEntity
    {
        $tokenEntity = $this->findByTokenAndName($token, $name);

        if (! $tokenEntity) {
            throw new TokenNotFoundException("Token is not found by '{$token}' and name '{$name}'");
        }

        return $tokenEntity;
    }

    /**
     * Find a token entity by a token string and token name.
     *
     * @param string $token
     * @param string $name
     * @return TokenEntity|null
     */
    public function findByTokenAndName(string $token, string $name): ?TokenEntity
    {
        return TokenEntity::where(compact('token', 'name'))->latest('id')->first();
    }

    /**
     * Find an active token for the given model with provided name.
     *
     * @param Model $model
     * @param string $name
     * @return TokenEntity|null
     */
    public function findActiveByNameFor(Model $model, string $name): ?TokenEntity
    {
        return TokenEntity::query()
            ->where('tokenable_id', $model->getKey())
            ->where('tokenable_type', get_class($model))
            ->where('name', $name)
            ->active()
            ->first();
    }
}
