<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): ?bool
    {
        $authHeader = $request->headers->get('Authorization');

        return $authHeader && substr($authHeader, 0, 7) === 'Bearer ';
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request): PassportInterface
    {
        $apiKey = substr($request->headers->get('Authorization'), 7);
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiKey, function($userIdentifier) {
                return $this->userRepository->findOneBy(['apiKey' => $userIdentifier]);
            })
        );
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}

