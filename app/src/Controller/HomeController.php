<?php

namespace App\Controller;

use App\Entity\Education;
use App\Entity\Language;
use App\Entity\User;
use App\Entity\Work;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    public static function isUserVerified(UserInterface $user) {
        if (!$user->isVerified()) {
            return false;
        }
        return true;
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if (!self::isUserVerified($this->getUser())) {
            return $this->render('error/generic.html.twig', [
                'errorTitle' => 'Ooops... It seems you have not verified your email address yet.',
                'errorBody' => 'Please check your email and follow the link we sent you to gain access here.',
                'errorAdditionalInfo' => 'Didn\'t receive it?',
                'errorAdditionalInfoLink' => 'http://localhost:8080/resendEmailVerify/' . $this->getUser()->getId(),
                'errorAdditionalInfoLinkButtonText' => 'Resend now!',
            ]);
        }

        $jobs = $entityManager->getRepository(Work::class)->findBy(['user' => $this->getUser()]);
        $education = $entityManager->getRepository(Education::class)->findBy(['user' => $this->getUser()]);
        $languages = $entityManager->getRepository(Language::class)->findBy(['user' => $this->getUser()]);

        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'jobs' => $jobs,
            'education' => $education,
            'languages' => $languages,
            'hasStarted' => $this->getUser()->isHasStarted(),
        ]);
    }

    #[Route('/legal', name: 'app_legal')]
    public function legal(): Response
    {
        return $this->render('legal/terms.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/legal/privacy', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('legal/privacy.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/getStarted', methods: ['POST'])]
    public function getStarted(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if (!self::isUserVerified($this->getUser())) {
            return $this->render('error/generic.html.twig', [
                'errorTitle' => 'Ooops... It seems you have not verified your email address yet.',
                'errorBody' => 'Please check your email and follow the link we sent you to gain access here.',
                'errorAdditionalInfo' => 'Didn\'t receive it?',
                'errorAdditionalInfoLink' => 'http://localhost:8080/resendEmailVerify/' . $this->getUser()->getId(),
                'errorAdditionalInfoLinkButtonText' => 'Resend now!',
            ]);
        }

        $user = $this->getUser();
        $user->setName($request->request->get('name'));
        $user->setCountry($request->request->get('country'));
        $user->setCity($request->request->get('city'));
        $user->setPhone($request->request->get('phone'));
        $user->setProfession($request->request->get('profession'));
        $user->setDescription($request->request->get('description'));
        $user->setHasStarted(true);

        $oldWork = $entityManager->getRepository(Work::class)->findBy(['user' => $user]);
        foreach ($oldWork as $oldWorkItem) {
            $entityManager->remove($oldWorkItem);
        }
        $entityManager->flush();

        $oldEdu = $entityManager->getRepository(Education::class)->findBy(['user' => $user]);
        foreach ($oldEdu as $oldEduItem) {
            $entityManager->remove($oldEduItem);
        }
        $entityManager->flush();

        $oldLanguages = $entityManager->getRepository(Language::class)->findBy(['user' => $user]);
        foreach ($oldLanguages as $oldLanguage) {
            $entityManager->remove($oldLanguage);
        }
        $entityManager->flush();

        for ($i = 1; $i <= intval($request->request->get('preloaded-work-size')); $i++) {
            $workItem = new Work();
            $workItem->setTitle($request->request->get('work-job-title-' . $i));
            $workItem->setCompany($request->request->get('work-company-' . $i));
            $workItem->setSalary($request->request->get('work-salary-' . $i));
            $workItem->setDescription($request->request->get('work-job-description-' . $i));
            $workItem->setUser($user);
            if ($request->request->get('work-job-isActive-' . $i) !== null) {
                $workItem->setIsActive(true);
            } else {
                $workItem->setIsActive(false);
                $date = new DateTimeImmutable($request->request->get('work-job-until-' . $i));
                $workItem->setUntil($date);
            }
            $date = new DateTimeImmutable($request->request->get('work-job-from-' . $i));
            $workItem->setSince($date);

            $entityManager->persist($workItem);
            $entityManager->flush();
        }


        for ($i = 1; $i <= intval($request->request->get('total-work-size')); $i++) {
            if ($request->request->get('job-title-' . $i) !== null) {
                $workItem = new Work();
                $workItem->setTitle($request->request->get('job-title-' . $i));
                $workItem->setCompany($request->request->get('company-' . $i));
                $workItem->setSalary($request->request->get('salary-' . $i));
                $workItem->setDescription($request->request->get('job-description-' . $i));
                $workItem->setUser($user);
                if ($request->request->get('job-isActive-' . $i) !== null) {
                    $workItem->setIsActive(true);
                } else {
                    $workItem->setIsActive(false);
                    $date = new DateTimeImmutable($request->request->get('job-until-' . $i));
                    $workItem->setUntil($date);
                }
                $date = new DateTimeImmutable($request->request->get('job-from-' . $i));
                $workItem->setSince($date);

                $entityManager->persist($workItem);
                $entityManager->flush();
            }
        }

        for ($i = 1; $i <= intval($request->request->get('preloaded-edu-size')); $i++) {
            if ($request->request->get('edu-institution-' . $i) !== null) {
                $eduItem = new Education();
                $eduItem->setInstitution($request->request->get('edu-institution-' . $i));
                $eduItem->setLocation($request->request->get('edu-location-' . $i));
                $eduItem->setProfile($request->request->get('edu-profile-' . $i));
                $eduItem->setDegree($request->request->get('edu-degree-' . $i));
                $date = new DateTimeImmutable($request->request->get('edu-graduated-on' . $i));
                $eduItem->setDate($date);
                $eduItem->setUser($user);

                $entityManager->persist($eduItem);
                $entityManager->flush();
            }
        }

        for ($i = 1; $i <= intval($request->request->get('total-edu-size')); $i++) {
            if ($request->request->get('institution-' . $i) !== null) {
                $eduItem = new Education();
                $eduItem->setInstitution($request->request->get('institution-' . $i));
                $eduItem->setLocation($request->request->get('location-' . $i));
                $eduItem->setProfile($request->request->get('profile-' . $i));
                $eduItem->setDegree($request->request->get('degree-' . $i));
                $date = new DateTimeImmutable($request->request->get('graduated-on' . $i));
                $eduItem->setDate($date);
                $eduItem->setUser($user);

                $entityManager->persist($eduItem);
                $entityManager->flush();
            }
        }
        for ($i = 1; $i <= intval($request->request->get('preloaded-language-size')); $i++) {
            if ($request->request->get('language-name-' . $i) !== null) {
                $languageItem = new Language();
                $languageItem->setName($request->request->get('language-name-' . $i));
                $languageItem->setLevel($request->request->get('language-level-' . $i));
                $languageItem->setUser($user);

                $entityManager->persist($languageItem);
                $entityManager->flush();
            }
        }

        for ($i = 1; $i <= intval($request->request->get('total-language-size')); $i++) {
            if ($request->request->get('name-' . $i) !== null) {
                $languageItem = new Language();
                $languageItem->setName($request->request->get('name-' . $i));
                $languageItem->setLevel($request->request->get('level-' . $i));
                $languageItem->setUser($user);

                $entityManager->persist($languageItem);
                $entityManager->flush();
            }
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('profile');
    }

    #[Route('/profile', name: 'profile')]
    public function profile(EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if (!self::isUserVerified($this->getUser())) {
            return $this->render('error/generic.html.twig', [
                'errorTitle' => 'Ooops... It seems you have not verified your email address yet.',
                'errorBody' => 'Please check your email and follow the link we sent you to gain access here.',
                'errorAdditionalInfo' => 'Didn\'t receive it?',
                'errorAdditionalInfoLink' => 'http://localhost:8080/resendEmailVerify/' . $this->getUser()->getId(),
                'errorAdditionalInfoLinkButtonText' => 'Resend now!',
            ]);
        }

        $user = $this->getUser();

        if ($user->isHasStarted()) {
            $jobs = $entityManager->getRepository(Work::class)->findBy(['user' => $user]);
            $education = $entityManager->getRepository(Education::class)->findBy(['user' => $user]);
            $languages = $entityManager->getRepository(Language::class)->findBy(['user' => $user]);

            return $this->render('cv/' . $user->getStyle() . '.html.twig', [
                'controller_name' => 'HomeController',
                'user' => $user,
                'jobs' => $jobs,
                'education' => $education,
                'languages' => $languages,
                'isOwner' => true,
            ]);
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/{id}', name: 'profileGuest')]
    public function profileGuest(EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        if ($user === null) {
            return $this->render('error/error.html.twig', [
                'controller_name' => 'HomeController',
                'message' => 'Oops! It seems like the user you\'re looking for doesn\'t exist!',
                'extension' => '../',
            ]);
        }

        if (!$user->isHasStarted()) {
            return $this->render('error/error.html.twig', [
                'controller_name' => 'HomeController',
                'message' => 'Oops! It seems like this user didn\'t set up his account yet!',
                'extension' => '../',
            ]);
        }

        $jobs = $entityManager->getRepository(Work::class)->findBy(['user' => $user]);
        $education = $entityManager->getRepository(Education::class)->findBy(['user' => $user]);
        $languages = $entityManager->getRepository(Language::class)->findBy(['user' => $user]);

        return $this->render('cv/' . $user->getStyle() . '.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'jobs' => $jobs,
            'education' => $education,
            'languages' => $languages,
            'isOwner' => false,
        ]);
    }

    #[Route('/changeProfilePicture', methods: ['POST'])]
    public function changeProfilePicture(EntityManagerInterface $entityManager, Request $request, KernelInterface $kernel): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if (!self::isUserVerified($this->getUser())) {
            return $this->render('error/generic.html.twig', [
                'errorTitle' => 'Ooops... It seems you have not verified your email address yet.',
                'errorBody' => 'Please check your email and follow the link we sent you to gain access here.',
                'errorAdditionalInfo' => 'Didn\'t receive it?',
                'errorAdditionalInfoLink' => 'http://localhost:8080/resendEmailVerify/' . $this->getUser()->getId(),
                'errorAdditionalInfoLinkButtonText' => 'Resend now!',
            ]);
        }

        $user = $this->getUser();
        if ($user === null) {
            return $this->render('error/error.html.twig', [
                'controller_name' => 'HomeController',
                'message' => 'Oops! It seems you are not logged in!',
                'extension' => '',
            ]);
        }

        if ($request->files->get('image') !== null) {
            $fileName = $user->getId() . '-' . $request->files->get('image')->getClientOriginalName();
            $request->files->get('image')->move($kernel->getProjectDir() . '/public/assets/img/profile-pictures/', $fileName);
            $user->setPicture('assets/img/profile-pictures/' . $fileName);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => true]);
    }

    #[Route('/changeTheme', methods: ['POST'])]
    public function changeTheme(EntityManagerInterface $entityManager, Request $request, KernelInterface $kernel): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        if (!self::isUserVerified($this->getUser())) {
            return $this->render('error/generic.html.twig', [
                'errorTitle' => 'Ooops... It seems you have not verified your email address yet.',
                'errorBody' => 'Please check your email and follow the link we sent you to gain access here.',
                'errorAdditionalInfo' => 'Didn\'t receive it?',
                'errorAdditionalInfoLink' => 'http://localhost:8080/resendEmailVerify/' . $this->getUser()->getId(),
                'errorAdditionalInfoLinkButtonText' => 'Resend now!',
            ]);
        }

        $user = $this->getUser();
        if ($user === null) {
            return $this->render('error/error.html.twig', [
                'controller_name' => 'HomeController',
                'message' => 'Oops! It seems you are not logged in!',
                'extension' => '',
            ]);
        }

        if ($request->request->get('theme') !== null) {
            $user->setStyle(intval($request->request->get('theme')));
        } else {
            return $this->render('error/error.html.twig', [
                'controller_name' => 'HomeController',
                'message' => 'Oops! It seems like the selected theme doesn\'t exist!',
                'extension' => '',
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['status' => true]);
    }
}
