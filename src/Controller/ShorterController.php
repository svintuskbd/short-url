<?php

namespace App\Controller;

use App\Entity\Url;
use App\Repository\UrlRepository;
use App\Service\UrlShorter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ShorterController extends Controller
{
    /**
     * @Route("/", name="make_link")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $shortLink = null;

        if ($request->isMethod('post')) {
            $url = new Url();
            $url->setLink($request->get('link'));
            $entityManager->persist($url);
            $entityManager->flush();
            $shorter = $this->get(UrlShorter::class);
            $shortUrl = $this->generateUrl('short_link', ['shortLink' => $shorter->encode($url->getId())], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return $this->render('shorter/index.html.twig', compact('shortUrl'));
    }

    /**
     * @Route("/{shortLink}", name="short_link")
     */
    public function shortLink($shortLink, UrlRepository $urlRepository)
    {
        $shorter = $this->get(UrlShorter::class);

        $url = $urlRepository->find($shorter->decode($shortLink));

        if (!$url) {
            throw $this->createNotFoundException();
        }

        return $this->redirect($url->getLink());
    }
}
