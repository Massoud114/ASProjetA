<?php

namespace App\Test\Controller;

use App\Application\Settings\Faq\Faq;
use App\Application\Settings\Faq\FaqRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FaqControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private FaqRepository $repository;
    private string $path = '/faq/';

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Faq index');

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
            'faq[createdAt]' => 'Testing',
            'faq[question]' => 'Testing',
            'faq[answer]' => 'Testing',
        ]);

        self::assertResponseRedirects('/faq/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Faq();
        $fixture->setCreatedAt('My Title');
        $fixture->setQuestion('My Title');
        $fixture->setAnswer('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Faq');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Faq();
        $fixture->setCreatedAt('My Title');
        $fixture->setQuestion('My Title');
        $fixture->setAnswer('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'faq[createdAt]' => 'Something New',
            'faq[question]' => 'Something New',
            'faq[answer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/faq/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getQuestion());
        self::assertSame('Something New', $fixture[0]->getAnswer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Faq();
        $fixture->setCreatedAt('My Title');
        $fixture->setQuestion('My Title');
        $fixture->setAnswer('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/faq/');
    }

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Faq::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }
}
