<?php

namespace Undine\AppBundle\Controller\Admin;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Undine\AppBundle\Controller\AppController;
use Undine\Form\Type\Admin\UserType;
use Undine\Model\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/user")
 */
class UserController extends AppController
{
    /**
     * @Method("GET")
     * @Route("", name="admin-user_list")
     * @Template("admin/user/list.html.twig")
     */
    public function listAction()
    {
        $users = $this->userRepository->findAll();

        return [
            'users' => $users,
        ];
    }

    /**
     * @Method("GET|POST")
     * @Route("/create", name="admin-user_create")
     * @Template("admin/user/create.html.twig")
     */
    public function createAction(Request $request)
    {
        $createForm = $this->createForm(UserType::class, null, [
            'method' => 'POST',
            'validation_groups' => ['create'],
        ]);

        $createForm->handleRequest($request);
        if ($createForm->isValid()) {
            /** @var User $user */
            $user = $createForm->getData();
            $this->em->persist($user);
            $this->em->flush($user);
            $this->addFlash('success', 'Staff member created.');

            return $this->redirectToRoute('admin-user_view', ['id' => $user->getId()]);
        }

        return [
            'createForm' => $createForm->createView(),
        ];
    }

    /**
     * @Method("GET|PUT")
     * @Route("/{id}/edit", name="admin-user_edit")
     * @ParamConverter("user", class="Model:User")
     * @Template("admin/user/edit.html.twig")
     */
    public function editAction(User $user, Request $request)
    {
        $editForm = $this->createForm(UserType::class, $user, [
            'method' => 'PUT',
        ]);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $this->em->persist($user);
            $this->em->flush($user);
            $this->addFlash('success', 'User info updated.');

            return $this->redirectToRoute('admin-user_view', ['id' => $user->getId()]);
        }

        return [
            'user' => $user,
            'editForm' => $editForm->createView(),
        ];
    }

    /**
     * @Method("GET|DELETE")
     * @Route("/{id}/delete", name="admin-user_delete")
     * @ParamConverter("user", class="Model:User")
     * @Template("admin/user/delete.html.twig")
     */
    public function deleteAction(User $user, Request $request)
    {
        $deleteForm = $this->createFormBuilder(null, [
            'method' => 'DELETE',
        ])
            ->add('id', HiddenType::class, ['data' => $user->getId()])
            ->getForm();

        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {
            $this->em->remove($user);
            $this->em->flush($user);
            $this->addFlash('success', 'User removed.');

            return $this->redirectToRoute('admin-user_list');
        }

        return [
            'user' => $user,
            'deleteForm' => $deleteForm->createView(),
        ];
    }

    /**
     * @Method("GET")
     * @Route("/{id}", name="admin-user_view")
     * @ParamConverter("user", class="Model:User")
     * @Template("admin/user/view.html.twig")
     */
    public function viewAction(User $user)
    {
        return [
            'user' => $user,
        ];
    }
}
