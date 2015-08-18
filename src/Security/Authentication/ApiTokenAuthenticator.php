<?php

namespace Undine\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Undine\Security\Authentication\Token\ApiToken;
use Undine\Security\User\ApiTokenUserProvider;

class ApiTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiTokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of %s (%s was given).',
                    ApiTokenUserProvider::class,
                    get_class($userProvider)
                )
            );
        }

        $apiToken = $token->getCredentials();
        $user = $userProvider->loadUserByApiToken($apiToken);

        return new ApiToken(
            $user,
            $apiToken,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof ApiToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->get('token');

        if (!$apiKey) {
            return null;
        }

        return new ApiToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    /**
     * Let the exception get handled by the ApiResultListener.
     *
     * @see ApiResultListener
     *
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw $exception;
    }
}
