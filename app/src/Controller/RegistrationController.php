<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Hybridauth\Provider\LinkedIn;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Check if email already exists
            $matchingEmail = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
            if ($matchingEmail) {
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'registrationError' => 'There is already an account with this email!',
                ]);
            }
            //Check if passwords match
            if ($form->get('plainPassword')->getData() == $form->get('repeatPlainPassword')->getData()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            } else {
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                    'registrationError' => 'Passwords do not match!',
                ]);
            }

            $user->setPicture("assets/img/profile.png");
            $user->setHasStarted(false);
            $user->setStyle(1);

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('changethis@entervalidemail.com', 'A.C.C.F.N.S. Mail Bot - Arrival'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/register/linkedin', name: 'app_register_linkedin')]
    public function registerLinkedin(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $config = [
            'callback' => 'http://localhost:8080/register/linkedin',
            'keys' => [
                'id' => '77qw3v48zz0zeh',
                'secret' => 'kRXAWgSNebmZtHfL'
            ],
            'scope' => 'r_liteprofile r_emailaddress',
        ];

        $adapter = new LinkedIn($config);
        $adapter->authenticate();
        $userProfile = $adapter->getUserProfile();
//        dd($userProfile);

        $preUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $userProfile->email]);
        if ($preUser === null) {
            $user = new User();
            $user->setEmail($userProfile->email);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    'asdasd'
                )
            );
            $user->setIsVerified(1);
            $user->setName($userProfile->displayName);
            $user->setCountry($userProfile->country);
            $user->setCity($userProfile->city);
            $user->setDescription($userProfile->description);
            $user->setPhone($userProfile->phone);
            $user->setPicture($userProfile->photoURL);
            $user->setHasStarted(false);
            $user->setStyle(1);
            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $userAuthenticator->authenticateUser(
            $preUser,
            $authenticator,
            $request
        );
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
