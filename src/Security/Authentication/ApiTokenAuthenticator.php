<?php

namespace Undine\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Undine\Security\Authentication\Token\ApiToken;
use Undine\Security\User\ApiTokenUserProvider;

class ApiTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * {@inheritdoc}
     */
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

        // Authenticated token.
        return new ApiToken($user, $apiToken, $providerKey, $user->getRoles());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof ApiToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritdoc}
     */
    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->get('token');

        if (!$apiKey) {
            // Let the anonymous firewall pick this up, it might be one of the unguarded API calls.
            return null;
        }

        // Non-authenticated token.
        return new ApiToken('anon.', $apiKey, $providerKey);
    }

    /**
     * Let the exception get handled by the ApiResultListener.
     * It can't implement AuthenticationFailureHandlerInterface because when using SimplePreAuthenticatorInterface
     * only the authenticator itself (in this case, this class) may implement it.
     *
     * @see ApiResultListener::onKernelException
     *
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw $exception;
    }
}
