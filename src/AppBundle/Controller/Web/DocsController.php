<?php

namespace Undine\AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Undine\AppBundle\Controller\AppController;

/**
 * @Route("/docs")
 */
class DocsController extends AppController
{
    /**
     * @Route("/api", name="web-docs_api")
     * @Template("web/docs/api.html.twig")
     */
    public function apiDevAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            return [];
        }

        $tokenManager = $this->get('undine.security.api_token_manager');
        $formAction   = $this->generateUrl('web-docs_api');

        if ($user->hasApiToken()) {
            $regenerateForm = $this->createNamedForm('regenerateToken', 'form', null, ['method' => 'POST', 'action' => $formAction]);
            $deleteForm     = $this->createNamedForm('deleteToken', 'form', null, ['method' => 'POST', 'action' => $formAction]);
            $regenerateForm->handleRequest($request);
            $deleteForm->handleRequest($request);

            if ($regenerateForm->isValid()) {
                $tokenManager->issueToken($user);
                $this->addFlash('success', "Token regenerated.");

                return $this->redirectToRoute('web-docs_api');
            } elseif ($deleteForm->isValid()) {
                $tokenManager->deleteToken($user);
                $this->addFlash('success', "Token deleted.");

                return $this->redirectToRoute('web-docs_api');
            }

            return [
                'token'          => $tokenManager->getToken($user),
                'regenerateForm' => $regenerateForm->createView(),
                'deleteForm'     => $deleteForm->createView(),
            ];
        } else {
            $createForm = $this->createNamedForm('createToken', 'form', null, ['method' => 'POST', 'action' => $formAction]);
            $createForm->handleRequest($request);

            if ($createForm->isValid()) {
                $tokenManager->issueToken($user);
                $this->addFlash('success', "Token created.");

                return $this->redirectToRoute('web-docs_api');
            }

            return [
                'createForm' => $createForm->createView(),
            ];
        }
    }
}
