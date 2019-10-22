<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @IsGranted("ROLE_USER")
 * @Route({
 *     "en": "/account",
 *     "fr": "/mon-compte"
 * }, name="account_")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route({
     *      "en": "/change-password",
     *      "fr": "/changement-du-mot-de-passe"
     * }, name="change_password")
     */
    public function changePassword(Request $request, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $translator->trans('controller.account.change_password.success');
            $this->addFlash('success', $message);

            return $this->redirectToRoute('account_index');
        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
