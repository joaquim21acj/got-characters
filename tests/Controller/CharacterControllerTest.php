<?php

namespace App\Test\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CharacterControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/character/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Character::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Character index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'character[name]' => 'Testing',
            'character[link]' => 'Testing',
            'character[imageThumb]' => 'Testing',
            'character[imageFull]' => 'Testing',
            'character[royal]' => 'Testing',
            'character[house]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setName('My Title');
        $fixture->setLink('My Title');
        $fixture->setImageThumb('My Title');
        $fixture->setImageFull('My Title');
        $fixture->setRoyal('My Title');
        $fixture->setHouse('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Character');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setName('Value');
        $fixture->setLink('Value');
        $fixture->setImageThumb('Value');
        $fixture->setImageFull('Value');
        $fixture->setRoyal('Value');
        $fixture->setHouse('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'character[name]' => 'Something New',
            'character[link]' => 'Something New',
            'character[imageThumb]' => 'Something New',
            'character[imageFull]' => 'Something New',
            'character[royal]' => 'Something New',
            'character[house]' => 'Something New',
        ]);

        self::assertResponseRedirects('/character/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getLink());
        self::assertSame('Something New', $fixture[0]->getImageThumb());
        self::assertSame('Something New', $fixture[0]->getImageFull());
        self::assertSame('Something New', $fixture[0]->getRoyal());
        self::assertSame('Something New', $fixture[0]->getHouse());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setName('Value');
        $fixture->setLink('Value');
        $fixture->setImageThumb('Value');
        $fixture->setImageFull('Value');
        $fixture->setRoyal('Value');
        $fixture->setHouse('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/character/');
        self::assertSame(0, $this->repository->count([]));
    }
}
