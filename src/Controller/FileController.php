<?php

namespace App\Controller;

use App\Config\{ParseState, ParseStatus};
use App\Entity\File;
use App\File\Upload\FileUploadHandler;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\{HttpFoundation\BinaryFileResponse,
    HttpFoundation\Request,
    HttpFoundation\Response,
    Messenger\MessageBusInterface};
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/files', name: 'api_files')]
class FileController extends AbstractApiController
{
    /**
     * List files
     *
     * @return Response
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        return new Response(
            $this->serializer->serialize(
                $this->documents->findAll(),
                'json',
                $this->context->toArray()
            ),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }

    /**
     * Upload file
     *
     * @param Request $request
     * @param MessageBusInterface $bus
     * @param FileUploadHandler $fileUploader
     * @return Response
     */
    #[Route('', name: 'upload', methods: ['POST'])]
    public function upload(Request $request, MessageBusInterface $bus, FileUploadHandler $fileUploader): Response
    {
        if (!($uploadedFile = $request->files->get('file')) instanceof UploadedFile) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        try {
            $fileUploader->upload($uploadedFile);
        } catch (ExceptionInterface $e) {
            $this->logger->info($e->getMessage());
        } catch (Exception $e) {
            $this->logger->info($e->getMessage());
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([], Response::HTTP_CREATED);
    }

    /**
     * Get file
     *
     * @param File $file
     * @return Response
     */
    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(#[MapEntity(expr: 'repository.findNextById(id)')] File $file): Response
    {
        $file->setParseState(ParseState::RequestedByParser);
        $this->files->save($file);

        file_put_contents($file->getSha1() . '.pdf', $file->getContent());

        return new BinaryFileResponse($file->getSha1() . '.pdf');
    }

    /**
     * Update file
     *
     * @param File $file
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(File $file, Request $request): Response
    {
        if (!$data = $this->validator->validate($request, ['result', 'status', 'errors'])) {
            return $this->json([], Response::HTTP_BAD_REQUEST);
        }

        $file->setParseStatus(strtolower($data['status']) === 'success' ? ParseStatus::Success : ParseStatus::Failed)
            ->setParseState(ParseState::FinishedByParser)
            ->setParseResult($data['result']);

        $this->files->save($file);

        return $this->json([]);
    }

    /**
     * Remove file
     *
     * @param File $file
     * @return Response
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(File $file): Response
    {
        if ($file->getParseState() === ParseState::Queued) {
            $this->files->save($file->setParseState(ParseState::Cancelled));
            return $this->json([], Response::HTTP_NO_CONTENT);
        }

        throw new NotFoundHttpException();
    }
}
