<?php

namespace Undine\AppBundle\Controller\Admin;

use Undine\AppBundle\Controller\AppController;
use Undine\Model\Staff;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/staff")
 */
class StaffController extends AppController
{
    /**
     * @Method("GET")
     * @Route("", name="admin-staff_list")
     * @Template("admin/staff/list.html.twig")
     */
    public function listAction()
    {
        $staffMembers = $this->staffRepository->findAll();

        return [
            'staffMembers' => $staffMembers,
        ];
    }

    /**
     * @Method("GET|POST")
     * @Route("/create", name="admin-staff_create")
     * @Template("admin/staff/create.html.twig")
     */
    public function createAction(Request $request)
    {
        $createForm = $this->createForm('admin__staff', null, [
            'method'            => 'POST',
            'validation_groups' => ['create'],
        ]);

        $createForm->handleRequest($request);
        if ($createForm->isValid()) {
            /** @var Staff $staff */
            $staff = $createForm->getData();
            $this->em->persist($staff);
            $this->em->flush($staff);
            $this->addFlash('success', 'Staff member created.');

            return $this->redirectToRoute('admin-staff_view', ['id' => $staff->getId()]);
        }

        return [
            'createForm' => $createForm->createView(),
        ];
    }

    /**
     * @Method("GET|PUT")
     * @Route("/{id}/edit", name="admin-staff_edit")
     * @ParamConverter("staff", class="Model:Staff")
     * @Template("admin/staff/edit.html.twig")
     */
    public function editAction(Staff $staff, Request $request)
    {
        $editForm = $this->createForm('admin__staff', $staff, [
            'method' => 'PUT',
        ]);

        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $this->em->persist($staff);
            $this->em->flush($staff);
            $this->addFlash('success', 'Staff info updated.');

            return $this->redirectToRoute('admin-staff_view', ['id' => $staff->getId()]);
        }

        return [
            'staff'    => $staff,
            'editForm' => $editForm->createView(),
        ];
    }

    /**
     * @Method("GET|DELETE")
     * @Route("/{id}/delete", name="admin-staff_delete")
     * @ParamConverter("staff", class="Model:Staff")
     * @Template("admin/staff/delete.html.twig")
     */
    public function deleteAction(Staff $staff, Request $request)
    {
        $deleteForm = $this->createFormBuilder(null, [
            'method' => 'DELETE',
        ])->getForm();

        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {
            $this->em->remove($staff);
            $this->em->flush($staff);
            $this->addFlash('success', "Staff member removed.");

            return $this->redirectToRoute('admin-staff_list');
        }

        return [
            'staff'      => $staff,
            'deleteForm' => $deleteForm->createView(),
        ];
    }

    /**
     * @Method("GET")
     * @Route("/{id}", name="admin-staff_view")
     * @ParamConverter("staff", class="Model:Staff")
     * @Template("admin/staff/view.html.twig")
     */
    public function viewAction(Staff $staff)
    {
        return [
            'staff' => $staff,
        ];
    }
}
