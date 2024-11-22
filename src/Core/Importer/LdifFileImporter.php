<?php

namespace App\Core\Importer;

use App\Core\Interface\IFileImporter;

final class LdifFileImporter implements IFileImporter
{
  protected $handle;

  function __construct(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
  {
    if (($this->handle = fopen($file->getPathname(), 'r')) === false) {
      throw new \Exception("Ldif file corrupted");
    }
  }

  function __destruct()
  {
    fclose($this->handle);
  }
  function getFileFormat() : string
  {
    return 'ldif';
  }

  function getNextRecord() : ?array
  {
    $row = [];

    while (($line = fgets($this->handle, 1000)) !== false) {

      $line = trim($line);

      if (empty($line)) {
        if (empty($row)) {
          return null;
        }
        return $row;
      }

      if (strstr($line, ":") === false) {
        continue;
      }

      [$col, $val] = explode(":", $line);
      $row[trim($col)] = trim($val);
    }

    return null;
  }
  function isFormatCorrect() : bool
  {
    return false;
  }

}