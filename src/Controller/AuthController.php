<?php

namespace EDB\AdminBundle\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EDB\AdminBundle\Entity\AbstractUser;
use EDB\AdminBundle\Security\GoogleHelper;
use EDB\AdminBundle\Util\StringUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;

class AuthController
{
    protected Environment $twig;
    protected RouterInterface $router;
    protected GoogleHelper $googleHelper;
    protected MailerInterface $mailer;
    protected TranslatorInterface $translator;
    protected EntityManagerInterface $entityManager;
    protected UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        Environment $twig,
        RouterInterface $router,
        GoogleHelper $googleHelper,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->twig = $twig;
        $this->router = $router;
        $this->googleHelper = $googleHelper;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function login(): Response
    {
        return new Response($this->twig->render('@EDBAdmin/login.html.twig'));
    }

    public function forgotPassword(Request $request): Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            $username = $request->request->get('_username');

            if (false === $this->checkIfEmail($username)) {
                $request->getSession()->getFlashBag()->add('danger', 'Invalid email address');

                return new Response($this->twig->render('@EDBAdmin/forgot_password.html.twig'));
            }

            $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'username' => $username
            ]);

            if (null !== $user) {
                $user->setResetPasswordToken(StringUtils::generateRandomString('reset-password-'));
                $this->entityManager->flush();

                $mail = new Email();
                $mail->subject($this->translator->trans('Reset password'));
                $mail->to(new Address('eduan.de.bruijne@gmail.com'));
                $mail->from(new Address('info@com.com'));
                $mail->html($this->twig->render(
                    '@EDBAdmin/mail/reset_password.html.twig',
                    ['user' => $user]
                ));

                $this->mailer->send($mail);
            }

            $request->getSession()->getFlashBag()->add('success', 'An Email has been sent to reset your password');

            return new RedirectResponse('/');
        }

        return new Response($this->twig->render('@EDBAdmin/forgot_password.html.twig'));
    }

    public function resetPassword(Request $request):  Response
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            $token = $request->request->get('_token');
            $newPassword = $request->request->get('_new_password');
            $newPasswordRepeat = $request->request->get('_new_password_repeat');

            if ($newPassword !== $newPasswordRepeat) {
                $request->getSession()->getFlashBag()->add('danger', 'Given password values are not the same');

                return new RedirectResponse(
                    $this->router->generate('reset_password', [
                        'token' => $token
                    ])
                );
            }

            $user = $this->fetchUserByToken($token);
            $user->setSalt(StringUtils::generateRandomString());
            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            $user->setResetPasswordToken(null);

            $this->entityManager->flush();

            $request->getSession()->getFlashBag()->add('success', 'Password has been reset successfully');

            return new RedirectResponse('/');
        }

        $token = $request->query->get('token');

        if (null === $token) {
            return new RedirectResponse('/');
        }

        $user = $this->fetchUserByToken($token);

        if (null === $user) {
            return new RedirectResponse('/');
        }

        return new Response($this->twig->render('@EDBAdmin/reset_password.html.twig'));
    }

    private function fetchUserByToken(string $token): ?AbstractUser
    {
        return $this->entityManager->getRepository(User::class)->findOneBy([
            'resetPasswordToken' => $token
        ]);
    }

    private function checkIfEmail(string $username): bool
    {
        return filter_var($username, FILTER_VALIDATE_EMAIL);
    }

    public function check(): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('dashboard'));
    }

    public function startGoogleLogin()
    {
        return new RedirectResponse($this->googleHelper->getLoginUrl());
    }

    public function logout()
    {
        return new RedirectResponse('/');
    }
}
