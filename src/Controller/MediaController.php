<?php

namespace EDB\AdminBundle\Controller;

use EDB\AdminBundle\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(MediaService $mediaService, EntityManagerInterface $entityManager, Environment $twig, string $mediaPath)
    {
        $this->mediaService = $mediaService;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->mediaPath = $mediaPath;
    }

    /**
     * @Route("/media/preview", name="media_preview", methods={"GET"})
     */
    public function renderPreview(Request $request): Response
    {
        $id = $request->query->get('id');
        if (empty($id)) {
            return new Response();
        }

        $media = $this->entityManager->getRepository(Media::class)->find($id);
        return new Response($this->twig->render('@EDBAdmin/media/preview.html.twig', [
            'object' => $media
        ]));
    }

    /**
     * @Route("/media/upload", name="media_upload", methods={"POST"})
     */
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

    /**
     * @Route("/media/render-original", name="media_upload_original_path", methods={"POST"})
     */
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

    /**
     * @Route("/media/list-all", name="media_modal_list", methods={"GET"})
     */
    public function list(Request $request): Response
    {
        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository(Media::class);
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
            'targetId' => $request->query->get('targetId')
        ]));
    }

    /**
     * @Route("/media/insert-media", name="media_insert", methods={"GET"})
     */
    public function insert(Request $request): Response
    {
        try {
            $width = $request->query->get('w');
            $height = $request->query->get('h');
            $media = $this->entityManager->getRepository(Media::class)->find(
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

    private function createMedia(Request $request): Media
    {
        $uploadedFile = $request->files->get('media');
        $media = $this->mediaService->handleUploadedFile($uploadedFile);
        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
