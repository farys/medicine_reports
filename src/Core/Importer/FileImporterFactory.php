<?php

namespace App\Core\Importer;

use App\Core\Interface\IFileImporter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileImporterFactory
{
  function createFileImporter(UploadedFile $file) : IFileImporter
  {
    $fileImporter = match ($file->getClientOriginalExtension()) {
      'csv' => new CsvFileImporter($file),
      'json' => new JsonFileImporter($file),
      'ldif' => new LdifFileImporter($file),
    };
    return $fileImporter;
  }
}