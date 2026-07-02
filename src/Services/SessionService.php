<?php

namespace App\Services;

use App\Exceptions\BadRequestException;
use App\Repositories\SessionsRepository;
use PDOException;
use Random\RandomException;
use function bin2hex;
use function date;
use function hash;
use function random_bytes;

readonly class SessionService {
    // Token validity lifetime (on the server/database)
    private const int|float ACCESS_TOKEN_EXPIRATION_TIME = 15 * 60; // 15 mins
    private const int|float REFRESH_TOKEN_EXPIRATION_TIME = 7 * 24 * 60 * 60; // 7 days
    // private const int|float ACCESS_TOKEN_EXPIRATION_TIME = 15; //15s
    // private const int|float REFRESH_TOKEN_EXPIRATION_TIME = 20; //10s

    // Cookie lifetime (in the browser)
    private const int|float ACCESS_TOKEN_COOKIE_EXPIRATION_TIME = 7 * 24 * 60 * 60; // 7 days
    private const int|float REFRESH_TOKEN_COOKIE_EXPIRATION_TIME = 7 * 24 * 60 * 60; // 7 days

    public function __construct(private SessionsRepository $repo) {}

    public function generate(string $user_id): array {
        $now = time();
        $access_expires_time = $now + self::ACCESS_TOKEN_EXPIRATION_TIME;
        $refresh_expires_time = $now + self::REFRESH_TOKEN_EXPIRATION_TIME;
        $access_cookie_expires_time = $now + self::ACCESS_TOKEN_COOKIE_EXPIRATION_TIME;
        $refresh_cookie_expires_time = $now + self::REFRESH_TOKEN_COOKIE_EXPIRATION_TIME;

        try {
            // Generate random tokens
            $plain_access_token = bin2hex(random_bytes(32));
            $plain_refresh_token = bin2hex(random_bytes(32));

            // Hash the tokens
            $hashed_access_token = hash('sha256', $plain_access_token);
            $hashed_refresh_token = hash('sha256', $plain_refresh_token);

            // Set expiration dates
            $access_expires_at = date('Y-m-d H:i:s', $access_expires_time);
            $refresh_expires_at = date('Y-m-d H:i:s', $refresh_expires_time);

            // Store the tokens in the database
            $this->repo->insert([$user_id, $hashed_access_token, $hashed_refresh_token, $access_expires_at, $refresh_expires_at]);

            return [
                'access_token' => [
                    'token' => $plain_access_token,
                    'expires_at' => $access_expires_time,
                    'cookie_expires_at' => $access_cookie_expires_time
                ],
                'refresh_token' => [
                    'token' => $plain_refresh_token,
                    'expires_at' => $refresh_expires_time,
                    'cookie_expires_at' => $refresh_cookie_expires_time
                ],
            ];
        } catch (RandomException $ex) {
            throw new BadRequestException('Failed to generate random token');
        } catch (PDOException $ex) {
            throw new BadRequestException('Failed to save session');
        }
    }

    public function revoke(string $user_id, string $hashed_access_token): void {
        try {
            $this->repo->revokeToken($user_id, $hashed_access_token);
        } catch (PDOException $ex) {
            throw new BadRequestException('Failed to revoke session');
        }
    }

    public function regrant(string $hashed_refresh_token): array {
        try {
            $session = $this->repo->findByRefreshToken($hashed_refresh_token);
            if (!$session)
                throw new BadRequestException('Invalid refresh token');

            $this->repo->revokeToken($session['user_id'], $session['access_token_hash']);

            return $this->generate($session['user_id']);
        } catch (PDOException $ex) {
            throw new BadRequestException('Failed to regrant session');
        }
    }
}