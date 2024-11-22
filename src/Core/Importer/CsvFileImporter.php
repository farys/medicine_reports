<?php

namespace App\Core\Importer;

use App\Core\Interface\IFileImporter;

final class CsvFileImporter implements IFileImporter
{
  protected array $columns;
  protected $handle;

  function __construct(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
  {
    if (($this->handle = fopen($file->getPathname(), 'r')) !== false) {

      $this->columns = fgetcsv($this->handle, 1000, "|");

      if ($this->columns === false) {
        throw new \Exception("Csv file corrupted");
      }
    }
  }

  function __destruct()
  {
    fclose($this->handle);
  }
  function getFileFormat() : string
  {
    return 'csv';
  }

  function getNextRecord() : ?array
  {
    $row = fgetcsv($this->handle, 1000, "|");

    if ($row === false) {
      return null;
    }

    $arrRecord = array_combine($this->columns, $row);
    return $arrRecord;

  }
  function isFormatCorrect() : bool
  {
    return false;
  }

}