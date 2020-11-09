<?php

namespace App\Controller\Api;

use App\Entity\Pokemon;
use App\Entity\PokemonType;
use App\Manager\PokemonManager;
use App\Paginator\PaginatorInterface;
use App\Repository\PokemonRepository;
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

final class PokemonController extends ApiController
{
    private PokemonManager $manager;

    private ValidatorInterface $validator;

    public function __construct(
        PokemonManager $manager,
        ValidatorInterface $validator
    ) {
        $this->manager   = $manager;
        $this->validator = $validator;
    }

    /**
     * @Rest\Get(
     *     path="/pokemons",
     *     name="api_pokemons_list"
     * )
     * @Rest\QueryParam(
     *     name="keyword",
     *     nullable=true,
     *     description="The keyword to search for name."
     * )
     * @Rest\QueryParam(
     *     name="main_type",
     *     nullable=true,
     *     description="The main pokemon type to search."
     * )
     * @Rest\QueryParam(
     *     name="secondary_type",
     *     nullable=true,
     *     description="The secondary pokemon type to search."
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
     *     description="Returns the pokemon list.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Items(ref=@Model(type=Pokemon::class))
     *     )
     * )
     * @OA\Tag(name="Pokemons")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"pokemon:read", "Default"})
     */
    public function index(
        PokemonRepository $repository,
        ParamFetcherInterface $paramFetcher,
        PaginatorInterface $paginator
    ): View {
        $pokemons = $repository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('main_type'),
            $paramFetcher->get('secondary_type'),
            $paramFetcher->get('order')
        );

        $paginatedCollection = $paginator->paginate(
            'api_pokemons_list',
            $pokemons,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->view($paginatedCollection, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     *     path="/pokemons",
     *     name="api_pokemons_create"
     * )
     * @ParamConverter(
     *     "pokemon",
     *     converter="fos_rest.request_body"
     * )
     * @ParamConverter(
     *     "pokemonMainType",
     *     converter="pokemon_type_converter"
     * )
     * @ParamConverter(
     *     "pokemonSecondaryType",
     *     converter="pokemon_type_converter"
     * )
     * @OA\RequestBody(
     *    @Model(type=Pokemon::class, groups={"pokemon:write"})
     * )
     * @OA\Tag(name="Pokemons")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"pokemon:read", "type:read", "Default"})
     */
    public function create(Pokemon $pokemon, PokemonType $pokemonMainType, ?PokemonType $pokemonSecondaryType): View
    {
        $pokemon->setMainType($pokemonMainType)
            ->setSecondaryType($pokemonSecondaryType);

        $violations = $this->validator->validate($pokemon);

        if (count($violations)) {
            return $this->badRequest($violations);
        }

        $this->manager->persist($pokemon);

        return $this->view($pokemon, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     *     path="/pokemons/{uuid}",
     *     name="api_pokemons_edit"
     * )
     * @Rest\Patch(
     *     path="/pokemons/{uuid}",
     *     name="api_pokemons_patch"
     * )
     * @ParamConverter(
     *     "pokemonMainType",
     *     converter="pokemon_type_converter"
     * )
     * @ParamConverter(
     *     "pokemonSecondaryType",
     *     converter="pokemon_type_converter"
     * )
     * @OA\RequestBody(
     *    @Model(type=Pokemon::class, groups={"pokemon:write"})
     * )
     * @OA\Tag(name="Pokemons")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"pokemon:read", "type:read", "Default"})
     */
    public function modify(
        Request $request,
        Pokemon $pokemon,
        ?PokemonType $pokemonMainType,
        ?PokemonType $pokemonSecondaryType
    ): View {
        $pokemon
            ->setName($request->request->get('name', $pokemon->getName()))
            ->setHitPoints($request->request->get('hit_points', $pokemon->getHitPoints()))
            ->setAttack($request->request->get('attack', $pokemon->getAttack()))
            ->setDefense($request->request->get('defense', $pokemon->getDefense()))
            ->setSpeedAttack($request->request->get('speed_attack', $pokemon->getSpeedAttack()))
            ->setSpeedDefense($request->request->get('speed_defense', $pokemon->getSpeedDefense()))
            ->setSpeed($request->request->get('speed', $pokemon->getSpeed()))
            ->setGeneration($request->request->get('generation', $pokemon->getGeneration()))
            ->setLegendary($request->request->get('legendary', $pokemon->getLegendary()))
            ->setMainType($pokemonMainType ?: $pokemon->getMainType())
            ->setSecondaryType($pokemonSecondaryType ?: $pokemon->getSecondaryType());

        $violations = $this->validator->validate($pokemon);

        if (count($violations)) {
            return $this->badRequest($violations);
        }

        $this->manager->update($pokemon);

        return $this->view($pokemon, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path="/pokemons/{uuid}",
     *     name="api_pokemons_show"
     * )
     * @OA\Tag(name="Pokemons")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"pokemon:read", "type:read", "Default"})
     */
    public function show(Pokemon $pokemon): View
    {
        return $this->view($pokemon, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path="/pokemons/{uuid}",
     *     name="api_pokemons_delete"
     * )
     * @OA\Tag(name="Pokemons")
     * @Security(name="Bearer")
     */
    public function delete(Pokemon $pokemon): View
    {
        $this->manager->remove($pokemon);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
