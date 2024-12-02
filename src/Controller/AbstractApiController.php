<?php

namespace App\Controller;

use App\{Repository\DocumentRepository, Repository\FileRepository, Request\RequestValidator};
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{Serializer\SerializerInterface};
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

abstract class AbstractApiController extends AbstractController
{
    public const string SERIALIZER_GROUP_NAME = 'API_ALL';

    protected ObjectNormalizerContextBuilder $context;

    public function __construct(
        protected EntityManagerInterface $em,
        protected FileRepository $files,
        protected DocumentRepository $documents,
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        protected RequestValidator $validator
    ) {
        $this->setContext();
    }

    /**
     * Sets the serializer context.
     *
     * @return void
     */
    public function setContext(): void
    {
        $this->context = (new ObjectNormalizerContextBuilder())
            ->withGroups(self::SERIALIZER_GROUP_NAME);
    }
}
