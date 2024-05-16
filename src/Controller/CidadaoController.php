<?php

namespace App\Controller;

use App\Entity\Cidadao;
use App\Form\CidadaoType;
use App\Repository\CidadaoRepository;
use App\Service\CidadaoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cidadao')]
class CidadaoController extends AbstractController
{
    #[Route('/', name: 'app_cidadao_index', methods: ['GET'])]
    public function index(CidadaoRepository $cidadaoRepository): Response
    {
        $cidadaos = $cidadaoRepository->findAll();
        return $this->render('cidadao/index.html.twig', [
            'cidadaos' => $cidadaos,
        ]);
    }

    #[Route('/new', name: 'app_cidadao_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cidadao = new Cidadao();
        $form = $this->createForm(CidadaoType::class, $cidadao);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cidadaoService = new CidadaoService($entityManager);

            if ($cidadaoService->insert($cidadao)) {
                $this->addFlash('success', 'Cidadão cadastrado com sucesso!');
            }

            return $this->redirectToRoute('app_cidadao_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cidadao/new.html.twig', [
            'cidadao' => $cidadao,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cidadao_show', methods: ['GET'])]
    public function show(Cidadao $cidadao): Response
    {
        return $this->render('cidadao/show.html.twig', [
            'cidadao' => $cidadao,
        ]);
    }

    #[Route('/seach', name: 'app_cidadao_seach', methods: ['POST'])]
    public function findNis(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nis =  $request->request->get('nis');

        $cidadaoService = new CidadaoService($entityManager);

        if($nis) $cidadao = $cidadaoService->seach($nis);


        return $this->render('cidadao/show.html.twig', [
            'cidadao' => $cidadao,
        ]);
    }
}
