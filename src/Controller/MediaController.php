<?php

namespace EDB\AdminBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use EDB\AdminBundle\Entity\AbstractMedia;
use EDB\AdminBundle\Service\MediaService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class MediaController
{
    private MediaService $mediaService;
    private EntityManagerInterface $entityManager;
    private Environment $twig;
    private string $mediaPath;
    private string $mediaClass;

    public function __construct(
        MediaService $mediaService,
        EntityManagerInterface $entityManager,
        Environment $twig,
        string $mediaPath,
        string $mediaClass
    )
    {
        if (!$mediaClass) {
            throw new Exception('No media class is implemented in this project.');
        }

        $this->mediaService = $mediaService;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->mediaPath = $mediaPath;
        $this->mediaClass = $mediaClass;
    }

    public function renderPreview(Request $request): Response
    {
        $id = $request->query->get('id');
        if (empty($id)) {
            return new Response();
        }

        $media = $this->entityManager->getRepository($this->mediaClass)->find($id);
        return new Response($this->twig->render('@EDBAdmin/media/preview.html.twig', [
            'object' => $media
        ]));
    }

    public function uploadFile(Request $request): JsonResponse
    {
        try {
            return new JsonResponse([
                'mediaId' => $this->createMedia($request)->getId()
            ], 200);
        } catch (Exception $exception) {
            return new JsonResponse([
                'error' => 'Something went wrong, try again.',
                'message' => $exception->__toString()
            ], 400);
        }
    }

    public function uploadFileGenerateOriginalPath(Request $request): JsonResponse
    {
        $media = $this->createMedia($request);
        $imageUrl = $this->server->makeImage($media->getFilename(), [
            'w' => 0,
            'h' => 0
        ]);

        return new JsonResponse([
            'path' => sprintf('%s/%s', rtrim($this->mediaPath), $imageUrl)
        ], 200);
    }

    public function list(Request $request): Response
    {
        $repository = $this->entityManager->getRepository($this->mediaClass);
        $queryBuilder = $repository->createQueryBuilder('m')->select('m');

        $query = $request->query->get('q');
        if (!empty($query)) {
            $queryBuilder
                ->andWhere('m.title LIKE :q')
                ->setParameter('q', '%'.$query.'%');
        }

        $mimeTypes = $request->query->all('m', []);
        if (!empty($mimeTypes)) {
            $queryBuilder
                ->andWhere('m.mimeType IN (:m)')
                ->setParameter('m', $mimeTypes);
        }

        $queryBuilder
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults(20);

        return new Response($this->twig->render('@EDBAdmin/media/list.html.twig', [
            'media' => $queryBuilder->getQuery()->getResult(),
            'targetId' => $request->query->get('t'),
            'params' => $request->query->all()
        ]));
    }

    public function insert(Request $request): Response
    {
        try {
            $width = $request->query->get('w');
            $height = $request->query->get('h');
            $media = $this->entityManager->getRepository($this->mediaClass)->find(
                $request->query->get('id')
            );

            $response = new Response($this->twig->render('@EDBAdmin/media/insert.html.twig', [
                'media' => $media,
                'width' => $width,
                'height' => $height,
            ]));
        } catch (Exception $e) {
            $response = new Response('');
        }

        return $response;
    }

    private function createMedia(Request $request): AbstractMedia
    {
        $uploadedFile = $request->files->get('media');
        $media = $this->mediaService->handleUploadedFile($uploadedFile);
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
