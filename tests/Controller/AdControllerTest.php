<?php

namespace App\Test\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AdRepository $repository;
    private string $path = '/ad/ad/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Ad::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ad index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ad[title]' => 'Testing',
            'ad[price]' => 'Testing',
            'ad[introduction]' => 'Testing',
            'ad[content]' => 'Testing',
            'ad[coverImage]' => 'Testing',
            'ad[rooms]' => 'Testing',
        ]);

        self::assertResponseRedirects('/ad/ad/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ad();
        $fixture->setTitle('My Title');
        $fixture->setPrice('My Title');
        $fixture->setIntroduction('My Title');
        $fixture->setContent('My Title');
        $fixture->setCoverImage('My Title');
        $fixture->setRooms('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Ad');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Ad();
        $fixture->setTitle('My Title');
        $fixture->setPrice('My Title');
        $fixture->setIntroduction('My Title');
        $fixture->setContent('My Title');
        $fixture->setCoverImage('My Title');
        $fixture->setRooms('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ad[title]' => 'Something New',
            'ad[price]' => 'Something New',
            'ad[introduction]' => 'Something New',
            'ad[content]' => 'Something New',
            'ad[coverImage]' => 'Something New',
            'ad[rooms]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ad/ad/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getPrice());
        self::assertSame('Something New', $fixture[0]->getIntroduction());
        self::assertSame('Something New', $fixture[0]->getContent());
        self::assertSame('Something New', $fixture[0]->getCoverImage());
        self::assertSame('Something New', $fixture[0]->getRooms());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Ad();
        $fixture->setTitle('My Title');
        $fixture->setPrice('My Title');
        $fixture->setIntroduction('My Title');
        $fixture->setContent('My Title');
        $fixture->setCoverImage('My Title');
        $fixture->setRooms('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/ad/ad/');
    }
}
