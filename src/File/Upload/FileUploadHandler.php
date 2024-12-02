<?php

namespace App\File\Upload;

use App\Config\ParseState;
use App\Entity\{Document, File};
use App\Event\Message\NewFileNotification;
use App\File\Reader\PdfFileReader;
use App\Repository\{DocumentRepository, FileRepository};
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * FileUploadHandler
 */
class FileUploadHandler
{

    public function __construct(protected FileRepository $files, protected DocumentRepository $documents, protected MessageBusInterface $bus)
    {
    }

    /**
     * Upload the file from the request (skipping if already exists).
     *
     * @param UploadedFile $file
     * @return Document
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function upload(UploadedFile $file): Document
    {
        $this->validate($file);
        $sha1 = sha1_file($file->getPathname());
        $fileRecord = $this->files->findOneBySha1($sha1);

        if (is_null($fileRecord)) {
            $fileRecord = (new File($file->getMimeType(), (new PdfFileReader($file->getPathname()))->read()))
                ->setSha1($sha1);

            $this->files->save($fileRecord);
            $this->bus->dispatch(new NewFileNotification($fileRecord->getId()));
        } elseif ($fileRecord->shouldReQueue()) {
            $fileRecord->setParseState(ParseState::Queued);
            $this->bus->dispatch(new NewFileNotification($fileRecord->getId()));
        }

        return $this->documents->save(new Document($fileRecord, $file->getClientOriginalName()));
    }

    /**
     * @param $file
     * @return void
     * @throws Exception
     */
    private function validate($file): void
    {
        if ($file->getMimeType() !== 'application/pdf') {
            throw new Exception('File must be a PDF file.');
        }

        if ($file->getSize() > 1024 * 1024 * 10) {
            throw new Exception('File must not be larger than 10MB.');
        }
    }
}
