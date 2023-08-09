<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\Ad1Type;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ad/ad')]
class AdAdController extends AbstractController
{
    #[Route('/', name: 'app_ad_ad_index', methods: ['GET'])]
    public function index(AdRepository $adRepository): Response
    {
        return $this->render('ad_ad/index.html.twig', [
            'ads' => $adRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ad_ad_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AdRepository $adRepository): Response
    {
        $ad = new Ad();
        $form = $this->createForm(Ad1Type::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adRepository->save($ad, true);

            return $this->redirectToRoute('app_ad_ad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ad_ad/new.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ad_ad_show', methods: ['GET'])]
    public function show(Ad $ad): Response
    {
        return $this->render('ad_ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ad_ad_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ad $ad, AdRepository $adRepository): Response
    {
        $form = $this->createForm(Ad1Type::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adRepository->save($ad, true);

            return $this->redirectToRoute('app_ad_ad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ad_ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ad_ad_delete', methods: ['POST'])]
    public function delete(Request $request, Ad $ad, AdRepository $adRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ad->getId(), $request->request->get('_token'))) {
            $adRepository->remove($ad, true);
        }

        return $this->redirectToRoute('app_ad_ad_index', [], Response::HTTP_SEE_OTHER);
    }
}
