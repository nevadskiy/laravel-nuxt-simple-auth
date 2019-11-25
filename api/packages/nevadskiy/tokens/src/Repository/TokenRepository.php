<?php

namespace Nevadskiy\Tokens\Repository;

use Illuminate\Database\Eloquent\Model;
use Nevadskiy\Tokens\Exceptions\TokenNotFoundException;
use Nevadskiy\Tokens\Token;

class TokenRepository
{
    /**
     * Get the token model by a token string and type.
     *
     * @param string $token
     * @param string $type
     * @return Token
     * @throws TokenNotFoundException
     */
    public function getByTokenType(string $token, string $type): Token
    {
        $tokenEntity = $this->findByTokenType($token, $type);

        if (! $tokenEntity) {
            throw new TokenNotFoundException("Token is not found by '{$token}' and type '{$type}'");
        }

        return $tokenEntity;
    }

    /**
     * Find the token entity a token string and type.
     *
     * @param string $token
     * @param string $type
     * @return Token|null
     */
    public function findByTokenType(string $token, string $type): ?Token
    {
        return Token::where(compact('token', 'type'))->latest('id')->first();
    }

    /**
     * Find an active token for the given model with provided type.
     *
     * @param Model $model
     * @param string $type
     * @return Token|null
     */
    public function findActiveByTypeFor(Model $model, string $type): ?Token
    {
        return Token::query()
            ->where('tokenable_id', $model->getKey())
            ->where('tokenable_type', get_class($model))
            ->where('type', $type)
            ->active()
            ->first();
    }
}
