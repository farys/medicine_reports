<?php

namespace App\Core\Interface;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface IFileImporter{
  public function __construct(UploadedFile $file);
  public function getFileFormat(): string;
  public function isFormatCorrect(): bool;
  public function getNextRecord(): ?array; 
}