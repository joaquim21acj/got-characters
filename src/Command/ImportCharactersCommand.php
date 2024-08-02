<?php

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Character;
use App\Entity\House;
use App\Repository\ActorRepository;
use App\Repository\HouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-user')]
class ImportCharactersCommand extends Command
{
    const LINK_REGEX = '/ch\d+/';
    private EntityManagerInterface $entityManager;
    private HouseRepository|EntityRepository $houseRepository;
    private ActorRepository|EntityRepository $actorRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->houseRepository = $entityManager->getRepository(House::class);
        $this->actorRepository = $entityManager->getRepository(Actor::class);

    }

    /**
     * @param $houseName
     * @param Character $character
     * @return void
     */
    public function handleHouseCharacter($houseName, Character $character): void
    {
        $house = $this->getHouse($houseName);
        $character->setHouse($house);
        $this->entityManager->persist($house);
    }

    /**
     * @param mixed $charData
     * @return Character
     */
    public function getCharacter(mixed $charData): Character
    {
        $character = new Character();
        $character->setName($charData['characterName']);
        $character->setLink($this->getLink($charData['characterLink'])); // Output: ch0305333
        $character->setRoyal($charData['royal'] ?? false);
        $character->setImageFull($charData['characterImageFull'] ?? null);
        $character->setImageThumb($charData['characterImageThumb'] ?? null);
        return $character;
    }

    /**
     * @param mixed $charData
     * @param Character $character
     * @return Actor
     */
    public function handleSingleActor(mixed $charData, Character $character): Actor
    {
        $actor = $this->getActor($charData);
        $actor->setCharacter($character);
        $this->entityManager->persist($actor);
        return $actor;
    }

    /**
     * @param $composedUrl
     * @return string|null
     */
    public function getLink($composedUrl): string | null
    {
        if (!$composedUrl) {
            return null;
        }
        preg_match(self::LINK_REGEX, $composedUrl, $matchesActorUrl);
        if (empty($matchesActorUrl)) {
            return null;
        }
        return $matchesActorUrl[0];
    }

    /**
     * @param $houseName
     * @return House
     */
    public function getHouse($houseName): House
    {
        $house = $this->houseRepository->findOneByName($houseName);
        if ($house) {
            return $house;
        }
        $house = new House();
        $house->setName($houseName);

        $this->entityManager->persist($house);
        return $house;
    }

    /**
     * @param mixed $charData
     * @return Actor
     */
    public function getActor(mixed $charData): Actor
    {
        $link = $this->getLink($charData['characterLink']);

        $actor = $this->actorRepository->findOneByLink($charData);
        if ($actor) {
            return $actor;
        }

        $actor = new Actor();
        $actor->setName($charData['actorName']);

        if ($link) {
            $actor->setLink($link);
        }
        return $actor;
    }

    protected function configure(): void
    {
        $this->setDescription('Imports characters from JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $json = file_get_contents('path/to/characters.json');
        $data = json_decode($json, true);

        foreach ($data['characters'] as $charData) {
            $character = $this->getCharacter($charData);

            if ($charData['houseName']) {
                if (is_array($charData['houseName'])) {
                    foreach ($charData['houseName'] as $houseName) {
                        $this->handleHouseCharacter($houseName, $character);
                    }
                } else {
                    $this->handleHouseCharacter($charData['houseName'], $character);
                }
            }

            if ($charData['actorName']) {
                $this->handleSingleActor($charData, $character);
            }

            if ($charData['actors']) {
                foreach ($charData['actors'] as $actorData) {
                    $this->handleSingleActor($actorData, $character);
                }
            }

            //TODO add other relations

            $this->entityManager->persist($character);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}