<?php

namespace App\File\Reader;

use Exception;

/**
 * PdfFileReader
 */
class PdfFileReader
{
    private string $pathToFile;

    /**
     * @throws Exception
     */
    public function __construct(string $pathToFile)
    {
        if (!is_readable($pathToFile)) {
            throw new Exception('File not found or unreadable');
        }

        $this->pathToFile = $pathToFile;

        return $this;
    }

    /**
     * @return string
     */
    public function read(): string
    {
        $fp = fopen($this->pathToFile, 'r');
        $data = fread($fp, filesize($this->pathToFile));
        fclose($fp);

        return $data;
    }
}
