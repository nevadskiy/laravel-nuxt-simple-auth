<?php

namespace Nevadskiy\Tokens\Tests\Support\Factory;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Nevadskiy\Tokens\Token;

class TokenFactory
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var Model
     */
    private $tokenable;

    /**
     * @var Carbon
     */
    private $expireDate;

    /**
     * @var Carbon
     */
    private $usageDate;

    /**
     * Create the token with provided parameters.
     *
     * @param string|null $token
     * @return Token
     */
    public function create(string $token = null): Token
    {
        $token = factory(Token::class)->make([
            'type' => $this->type ?: 'verification',
            'used_at' => null,
            'expired_at' => now()->addMonth(),
            'token' => $token ?: Str::random(10),
        ]);

        if ($this->tokenable) {
            $token->fillTokenable($this->tokenable);
        }

        if ($this->expireDate) {
            $token->expired_at = $this->expireDate;
        }

        if ($this->usageDate) {
            $token->used_at = $this->usageDate;
        }

        $token->save();

        return $token;
    }

    /**
     * Set a tokenable model which token will be created for.
     *
     * @param Model $tokenable
     * @return $this
     */
    public function for(Model $tokenable): self
    {
        $this->tokenable = $tokenable;

        return $this;
    }

    /**
     * Set a token type.
     *
     * @param string $type
     * @return $this
     */
    public function ofType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set expire date.
     *
     * @param Carbon|null $expireDate
     * @return $this
     */
    public function expired(Carbon $expireDate = null): self
    {
        $this->expireDate = $expireDate ?: Carbon::now()->subMinute();

        return $this;
    }

    /**
     * Set usage date.
     *
     * @param Carbon|null $usageDate
     * @return $this
     */
    public function used(Carbon $usageDate = null): self
    {
        $this->usageDate = $usageDate ?: Carbon::now();

        return $this;
    }
}
