<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserController
 * @package App\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserController
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var UserPasswordEncoderInterface */
    protected $encoder;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var FlashBagInterface */
    protected $flash;

    /** @var Environment */
    protected $twig;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;
    
    /** @var Security */
    protected $security;

    /**
     * UserController constructor.
     * @param FormFactoryInterface $formFactory
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flash
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param Security $security
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flash,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        Security $security
    ) {
        $this->formFactory = $formFactory;
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
        $this->flash = $flash;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    /**
     * @Route("/users", name="user_list")
     * @param UserRepository $userRepository
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(UserRepository $userRepository): Response
    {
        return new Response($this->twig->render('user/list.html.twig', ['users' => $userRepository->findAll()]));
    }
   

    /**
     * @Route("/users/create", name="user_create")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        $form = $this->formFactory->create(UserType::class, $user = new User())
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->flash->add('success', "L'utilisateur a bien été ajouté.");
            
            return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new Response($this->twig->render('user/create.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(User $user, Request $request)
    {
        $form = $this->formFactory->create(UserType::class, $user)
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $this->entityManager->flush();
            $this->flash->add('success', "L'utilisateur a bien été modifié");

            return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new Response($this->twig->render(
            'user/edit.html.twig',
            ['form' => $form->createView(), 'user' => $user]
        ));
    }
    /**
     * @Route("/user/{id}/delete", name="user_delete")
     * @param User $user
     * @return RedirectResponse
     */
    public function delete(User $user): RedirectResponse
    {
      //  if (!$this->security->isGranted('DELETE', $user)) {
       //     throw new AccessDeniedException();
      //  }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        $this->flash->add('error', 'L utilisateur a bien été supprimée.');

        return new RedirectResponse($this->urlGenerator->generate('user_list'));
    }
}

