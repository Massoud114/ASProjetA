<?php

namespace App\Infrastructure\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
	private $targetDirectory;
	private SluggerInterface $slugger;

	public function __construct($targetDirectory, SluggerInterface $slugger)
	{
		$this->targetDirectory = $targetDirectory;
		$this->slugger = $slugger;
	}

	/**
	 * @throws \Exception
	 */
	public function upload(UploadedFile $file): string
	{
		$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
		$fileName = uniqid() . '.' . $file->guessExtension();

		try {
			$file->move($this->getTargetDirectory(), $fileName);
		} catch (FileException $e) {
			throw new \Exception($e->getMessage());
		}

		return $fileName;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}
}
