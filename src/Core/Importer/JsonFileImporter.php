<?php

namespace App\Core\Importer;

use App\Core\Interface\IFileImporter;

final class JsonFileImporter implements IFileImporter
{
  protected array $columns;
  protected array $data;
  protected int $currentRow;

  function __construct(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
  {
    $content = file_get_contents($file->getPathname());
    $content = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      throw new \Exception('Invalid file format');
    }

    if (! $content ||
      empty($content['data']) ||
      empty($content['cols']) ||
      ! is_array($content['cols']) ||
      ! is_array($content['data'])) {
      throw new \Exception('Invalid file format');
    }

    $this->columns = (array) $content['cols'];
    $this->data = (array) $content['data'];
    $this->currentRow = 0;
  }

  function getFileFormat() : string
  {
    return 'json';
  }

  function getNextRecord() : ?array
  {
    if (empty($this->data[$this->currentRow]))
      return null;
    
    $arrRecord = array_combine($this->columns, $this->data[$this->currentRow]);
    $this->currentRow++;

    return $arrRecord;
  }

  function isFormatCorrect() : bool
  {
    return false;
  }

}