<?php

namespace App\Controller\Api;

use App\Entity\Album;
use OpenApi\Attributes as OA;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OA\Tag(name: "Album")]
class AlbumController extends AbstractController
{

    public function __construct(
        private AlbumRepository $albumRepository,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    )
    {
        // ...
    }


    #[Route('/api/albums', name: 'app_api_album', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: album::class, groups: ['read']))
        )
    )]
    public function index(
        PaginatorInterface $paginator, 
        Request $request
    ): JsonResponse
    {
        $albums = $this->albumRepository->getAllalbumsQuery();

        $data = $paginator->paginate(
            $albums,
            $request->query->get('page', 1),
            2
        );

        return $this->json([
            'data' => $data,
        ], 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/album/{id}', name: 'app_api_album_get',  methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Album::class, groups: ['read'])
    )]
    public function get(?Album $album = null): JsonResponse
    {
        if(!$album)
        {
            return $this->json([
                'error' => 'Ressource does not exist',
            ], 404);
        }

        return $this->json($album, 200, [], [
            'groups' => ['read']
        ]);
    }

    #[Route('/api/albums', name: 'app_api_album_add',  methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: album::class, groups: ['read'])
    )]
    public function add(
        #[MapRequestPayload('json', ['groups' => ['create']])] album $album
    ): JsonResponse
    {
        $this->em->persist($album);
        $this->em->flush();
        
        return $this->json($album, 200, [], [
            'groups' => ['read']
        ]);
    }

    
    #[Route('/api/album/{id}', name: 'app_api_album_update',  methods: ['PUT'])]
    #[OA\Put(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: Album::class, 
                    groups: ['update']
                )
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Album::class, groups: ['read'])
    )]
    public function update(
        Album $album, 
        Request $request
    ): JsonResponse
    {
        
        $data = $request->getContent();
        $this->serializer->deserialize($data, album::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $album,
            'groups' => ['update']
        ]);

        $this->em->flush();

        return $this->json($album, 200, [], [
            'groups' => ['read'],
        ]);
    }

    #[Route('/api/album/{id}', name: 'app_api_album_delete',  methods: ['DELETE'])]
    public function delete(Album $album): JsonResponse
    {
        $this->em->remove($album);
        $this->em->flush();

        return $this->json([
            'message' => 'album deleted successfully'
        ], 200);
    }

}
