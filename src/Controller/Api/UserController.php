<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Manager\UserManager;
use App\Paginator\PaginatorInterface;
use App\Repository\UserRepository;
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

final class UserController extends ApiController
{
    private UserManager $userManager;

    private ValidatorInterface $validator;

    public function __construct(
        UserManager $userManager,
        ValidatorInterface $validator
    ) {
        $this->userManager = $userManager;
        $this->validator   = $validator;
    }

    /**
     * @Rest\Get(
     *     path="/users",
     *     name="api_users_list"
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
     *     description="Returns the users list.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"user:read", "Default"})
     */
    public function index(
        ParamFetcherInterface $paramFetcher,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): View {
        $users = $userRepository->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order')
        );

        $paginatedCollection = $paginator->paginate(
            'api_pokemons_list',
            $users,
            $paramFetcher->get('page'),
            $paramFetcher->get('limit')
        );

        return $this->view($paginatedCollection, Response::HTTP_OK);
    }

    /**
     * @Rest\Post(
     *     path="/users",
     *     name="api_users_create"
     * )
     * @ParamConverter(
     *     "user",
     *     converter="fos_rest.request_body"
     * )
     * @OA\RequestBody(
     *    @Model(type=User::class, groups={"user:write", "user:password"})
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"user:read", "Default"})
     */
    public function create(User $user): View
    {
        $violations = $this->validator->validate($user);

        if (count($violations)) {
            return $this->badRequest($violations);
        }

        $this->userManager->persist($user);

        return $this->view($user, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Put(
     *     path="/users/{uuid}",
     *     name="api_users_edit"
     * )
     * @Rest\Patch(
     *     path="/users/{uuid}",
     *     name="api_users_patch"
     * )
     * @OA\RequestBody(
     *    @Model(type=User::class, groups={"user:write"})
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"user:read", "Default"})
     */
    public function modify(Request $request, User $user): View
    {
        $user
            ->setUsername($request->request->get('username', $user->getRealUsername()))
            ->setEmail($request->request->get('email', $user->getEmail()));

        $violations = $this->validator->validate($user);

        if (count($violations)) {
            return $this->badRequest($violations);
        }

        $this->userManager->update($user);

        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path="/users/{uuid}",
     *     name="api_users_show"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     * @Rest\View(serializerGroups={"user:read", "Default"})
     */
    public function show(User $user): View
    {
        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete(
     *     path="/users/{uuid}",
     *     name="api_users_delete"
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function delete(User $user): View
    {
        $this->userManager->remove($user);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
