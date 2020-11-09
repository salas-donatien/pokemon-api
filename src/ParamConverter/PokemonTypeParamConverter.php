<?php

namespace App\ParamConverter;

use App\Repository\PokemonTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class PokemonTypeParamConverter implements ParamConverterInterface
{
    private const POKEMON_MAIN_TYPE      = 'pokemonMainType';
    private const POKEMON_SECONDARY_TYPE = 'pokemonSecondaryType';

    private const POKEMON_TYPES = [
        self::POKEMON_MAIN_TYPE,
        self::POKEMON_SECONDARY_TYPE,
    ];

    private PokemonTypeRepository $repository;

    public function __construct(PokemonTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function apply(Request $request, ParamConverter $configuration): void
    {
        if ($configuration->getName() === self::POKEMON_MAIN_TYPE) {
            $type = $request->request->get('main_type')['type'] ?? '';
        } elseif ($configuration->getName() === self::POKEMON_SECONDARY_TYPE) {
            $type = $request->request->get('secondary_type')['type'] ?? '';
        }

        $pokemonType = $this
            ->repository
            ->findOneBy(['type' => trim($type) ?: '']);

        $request->attributes->set($configuration->getName(), $pokemonType);
    }

    public function supports(ParamConverter $configuration): bool
    {
        return null !== $configuration->getClass() &&
            in_array($configuration->getName(), self::POKEMON_TYPES, true);
    }
}
