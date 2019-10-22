<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EmailType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordType;
use App\Security\UserAuthenticator;
use App\Service\MailSender;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserSecurityController extends AbstractController
{
    /**
     * @Route({
     *      "en": "/login",
     *      "fr": "/connexion"
     *  },
     *  name="login",
     *  methods={"GET", "POST"}
     * )
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
            $this->redirectToRoute('homepage');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user_security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route({
     *      "en": "/logout",
     *      "fr": "/deconnexion"
     *  },
     *  name="logout",
     *  methods={"GET"}
     * )
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route({
     *      "en": "/register",
     *      "fr": "/enregistrement"
     *  },
     *  name="register",
     *  methods={"GET", "POST"}
     * )
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $generator, MailSender $sender): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setActivateToken($generator->generateToken());
            $user->setCreationDate(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $sender->activate($user);

            return $this->redirectToRoute('wait_for_activation');
        }

        return $this->render('user_security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route({
     *      "en": "/wait-for-activation",
     *      "fr": "/attente-de-l-activation"
     *  },
     *  name="wait_for_activation",
     *  methods={"GET", "POST"}
     * )
     */
    public function waitActivation(): Response
    {
        return $this->render('user_security/wait_for_activation.html.twig');
    }

    /**
     * @Route({
     *      "en": "/activate/{activateToken}",
     *      "fr": "/activation/{activateToken}"
     *  },
     *  name="activate",
     *  methods={"GET", "POST"}
     * )
     */
    public function activate(User $user, Request $request, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user->setActive(true);
        $user->setActivateToken(null);
        $this->getDoctrine()->getManager()->flush();

        return $guardHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
    }

    /**
     * @Route({
     *      "en": "/reset-password",
     *      "fr": "/reinitialisation-du-mot-de-passe"
     *  },
     *  name="reset_password_request",
     *  methods={"GET", "POST"}
     * )
     */
    public function resetPasswordRequest(Request $request, TranslatorInterface $translator, TokenGeneratorInterface $generator, MailSender $sender): Response
    {
        $form = $this->createForm(EmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailForm = $form->get('email');
            $email = $emailForm->getData();

            /** @var User|null $user */
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user === null) {
                $errorMessage = $translator->trans('controller.user_security.reset_password_request.user_not_found');
                $error = new FormError($errorMessage);
                $emailForm->addError($error);

                return $this->render('user_security/reset_password_request.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $token = $generator->generateToken();
            $user->setResetToken($token);
            $this->getDoctrine()->getManager()->flush();

            $sender->resetPassword($user);
            $successMessage = $translator->trans('controller.user_security.reset_password_request.email_send', [
                '%email%' => $email,
            ]);
            $this->addFlash('info', $successMessage);
        }

        return $this->render('user_security/reset_password_request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route({
     *      "en": "/reset-password/{resetToken}",
     *      "fr": "/reinitialisation-du-mot-de-passe/{resetToken}"
     *  },
     *  name="reset_password_confirmation",
     *  methods={"GET", "POST"}
     * )
     */
    public function resetPasswordConfirmation(User $user, Request $request): Response
    {
        $form = $this->createForm(ResetPasswordType::class, null, ['verify_old_password' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('user_security/reset_password_confirmation.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
