<?php

namespace EDB\AdminBundle\Controller;

use EDB\AdminBundle\Entity\Media;
use EDB\AdminBundle\Util\StringUtils;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use DateTime;
use League\Flysystem\FilesystemOperator;
use League\Glide\Server;

class MediaController
{
    private FilesystemOperator $privateFilesystem;
    private EntityManagerInterface $entityManager;
    private Environment $twig;
    private Server $server;
    private string $mediaPath;

    public function __construct(FilesystemOperator $privateFilesystem, FilesystemOperator $publicFilesystem, EntityManagerInterface $entityManager, Environment $twig, Server $server, string $mediaPath)
    {
        $this->privateFilesystem = $privateFilesystem;
        $this->publicFilesystem = $publicFilesystem;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->server = $server;
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
     * @Route("/media/list-all", name="media_list", methods={"GET"})
     */
    public function list(Request $request): Response
    {
        $instances = $this->entityManager->getRepository(Media::class)->findAll();

        return new Response($this->twig->render('@EDBAdmin/media/list.html.twig', [
            'media' => $instances
        ]));
    }

    private function createMedia(Request $request): Media
    {
        $uploadedFile = $request->files->get('image');
        $filename = $uploadedFile->getClientOriginalName();
        $mimetype = $uploadedFile->getClientMimeType();
        $size = $uploadedFile->getSize();
        $extension = $uploadedFile->getClientOriginalExtension();

        $newFilename = StringUtils::generateRandomString();
        $this->privateFilesystem->write(
            $newFilename,
            file_get_contents($uploadedFile->getRealPath())
        );

        $media = new Media();
        $media->setTitle($filename);
        $media->setFilename($newFilename);
        $media->setExtension($extension);
        $media->setMimeType($mimetype);
        $media->setSize($size);

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        return $media;
    }
}
