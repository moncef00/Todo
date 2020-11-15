<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Form\InscriptionType;
use App\Entity\User;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController
{

    /** @var FormFactoryInterface */
    protected $formFactory;
    /** @var UserPasswordEncoderInterface */
    protected $encoder;
       /** @var Security */
       protected $security;

    /** @var AuthenticationUtils */
    protected $authenticationUtils;
    
    /** @var EntityManagerInterface */
    protected $entityManager;
      /** @var FlashBagInterface */
      protected $flash;

    /** @var Environment */
    protected $twig;
    /**
     * SecurityController constructor.
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
        AuthenticationUtils $authenticationUtils,
        Environment $twig,
        Security $security,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flash,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
        $this->twig = $twig;
        $this->security = $security;
        $this->encoder = $encoder;
        $this->entityManager = $entityManager;
        $this->flash = $flash;
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(): Response
    {
        $form = $this->formFactory->create(LoginType::class);

        return new Response(
            $this->twig->render(
                'security/login.html.twig',
                [
                    'last_username' => $this->authenticationUtils->getLastUsername(),
                    'error'         => $this->authenticationUtils->getLastAuthenticationError(),
                    'form' => $form->createView()
                ]
            )
        );
    }
     /**
     * @Route("/inscription", name="inscription")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function inscription(Request $request)
    {
        $form = $this->formFactory->create(InscriptionType::class, $user = new User())
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->flash->add('success', "Inscription terminer avec succes");
            
         //return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new Response($this->twig->render('security/inscription.html.twig', ['form' => $form->createView()]));
    }
}
