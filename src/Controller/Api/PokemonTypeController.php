<?php

namespace App\Controller\Api;

use App\Entity\PokemonType;
use App\Manager\PokemonTypeManager;
use App\Paginator\PaginatorInterface;
use App\Repository\PokemonTypeRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PokemonTypeController extends ApiController
{
    private PokemonTypeManager $manager;

    private ValidatorInterface $validator;

    public function __construct(
        PokemonTypeManager $manager,
        ValidatorInterface $validator
    ) {
        $this->manager   = $manager;
        $this->validator = $validator;
    }

    /**
     * @Rest\Get(
     *     path="/pokemon_types",
     *     name="api_pokemon_types_list"
     * )
     * @Rest\QueryParam(
     *     name="keyword",
     *     nullable=true,
     *     description="The keyword to search."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     nullable=true,
     *     description="Sort order (asc or desc)."
     * )
     * @Rest\QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="Current page."
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="60",
     *     description="Max number of items per page."
     * )
     * @OA\Response(
     *     response=Response::HTTP_OK,
     *     description="Returns the types of pokemon.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Items(ref=@Model(type=PokemonType::class, groups={"type:read"}))
     *     )
     * )
     * @OA\Tag(name="Types of pokemon")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"type:read", "Default"})
     */
    public function index(
        PokemonTypeRepository $repository,
        ParamFetcherInterface $paramFetcher,
        PaginatorInterface $paginator
    ): View {
        $pokemonTypes = $repository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order')
        );

        $paginatedCollection = $paginator->paginate(
            'api_pokemon_types_list',
            $pokemonTypes,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->view($paginatedCollection, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     *     path="/pokemon_types",
     *     name="api_pokemon_types_create"
     * )
     * @ParamConverter(
     *     "pokemonType",
     *     converter="fos_rest.request_body"
     * )
     * @OA\RequestBody(
     *    @Model(type=PokemonType::class, groups={"type:write"})
     * )
     * @OA\Tag(name="Types of pokemon")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"type:read", "Default"})
     */
    public function create(PokemonType $pokemonType): View
    {
        $violations = $this->validator->validate($pokemonType);

        if (count($violations)) {
            return $this->badRequest($violations);
        }

        $this->manager->persist($pokemonType);

        return $this->view($pokemonType, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Get(
     *     path="/pokemon_types/{uuid}",
     *     name="api_pokemon_types_show"
     * )
     * @OA\Tag(name="Types of pokemon")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"type:read", "Default"})
     */
    public function show(PokemonType $pokemonType): View
    {
        return $this->view($pokemonType, Response::HTTP_OK);
    }

    /**
     * @Rest\Put(
     *     path="/pokemon_types/{uuid}",
     *     name="api_pokemon_types_edit"
     * )
     * @OA\RequestBody(
     *    @Model(type=PokemonType::class, groups={"type:write"})
     * )
     * @OA\Tag(name="Types of pokemon")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"type:read", "Default"})
     */
    public function edit(Request $request, PokemonType $pokemonType): View
    {
        $pokemonType->setType($request->request->get('type'));

        $violations = $this->validator->validate($pokemonType);

        if (count($violations)) {
            return $this->badRequest($violations);
        }

        $this->manager->update($pokemonType);

        return $this->view($pokemonType, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path="/pokemon_types/{uuid}",
     *     name="api_pokemon_types_delete"
     * )
     * @OA\Tag(name="Types of pokemon")
     * @Security(name="Bearer")
     */
    public function delete(PokemonType $pokemonType): View
    {
        $this->manager->remove($pokemonType);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
