<?php


namespace Creonit\MediaBundle\Service;


use Creonit\MediaBundle\Exception\NotPublicFileException;
use Creonit\MediaBundle\Exception\VideoSourceIsNotSupportedException;
use Creonit\MediaBundle\File\WrappedFile;
use Creonit\MediaBundle\Model;
use Creonit\MediaBundle\VideoHandler\VideoHandlerInterface;
use Creonit\MediaBundle\VideoHandler\VideoResolver;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaService
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var VideoResolver
     */
    protected $videoResolver;

    protected $projectDir;

    /**
     * @param TranslatorInterface $translator
     * @param VideoResolver $videoResolver
     * @param $projectDir
     */
    public function __construct(
        TranslatorInterface $translator,
        VideoResolver $videoResolver,
        $projectDir
    )
    {
        $this->translator = $translator;
        $this->projectDir = $projectDir;
        $this->videoResolver = $videoResolver;
    }

    public function uploadFile(UploadedFile $uploadedFile, $pathPrefix = '')
    {
        $name = md5(uniqid()) . '.' . $uploadedFile->getClientOriginalExtension();
        $path = ($pathPrefix ? '/' . trim(trim($pathPrefix), '/') : '') . $this->getFilePathPrefix($name);
        $originalName = $uploadedFile->getClientOriginalName();

        $file = $uploadedFile->move($this->getUploadsDir() . $path, $name);
        $file = new WrappedFile($file->getPathname());
        $file->setOriginalName($originalName);

        return $file;
    }

    public function getFilePathPrefix($filename, $depth = 3)
    {
        $path = [];

        $filename = preg_replace('/[^a-z0-9]+/i', '', basename($filename));
        $length = strlen($filename);

        if ($length < 1) {
            return '';
        }

        for ($i = 0; $i < min($length, $depth); $i++) {
            $path[] = $filename[$i];
        }

        return '/' . implode('/', $path);
    }

    /**
     * @param File $file
     * @return Model\File
     * @throws NotPublicFileException
     */
    public function createFile(File $file)
    {
        if (substr($file->getPath(), 0, strlen($this->getPublicDir())) !== $this->getPublicDir()) {
            throw new NotPublicFileException();
        }

        $mediaFile = new Model\File();
        $mediaFile->setOriginalName($file instanceof WrappedFile ? $file->getOriginalName() : $file->getFilename());
        $mediaFile->setPath($this->getRelativeUrl($file->getPath()));
        $mediaFile->setName($file->getFilename());
        $mediaFile->setExtension($file->getExtension());
        $mediaFile->setSize($file->getSize());
        $mediaFile->setMime($file->getMimeType());

        return $mediaFile;
    }

    /**
     * @param Model\File $file
     * @return File
     */
    public function openFile(Model\File $file)
    {
        return new File($this->getPublicDir() . $file->getUrl());
    }

    /**
     * @param $relativePath
     * @return File
     */
    public function openFileByRelativePath($relativePath)
    {
        return new File($this->getPublicDir() . '/' . ltrim($relativePath, '/'));
    }

    protected function getPublicDir()
    {
        return $this->projectDir . '/public';
    }

    public function getUploadsDir()
    {
        return $this->getPublicDir() . '/uploads';
    }

    public function getRelativeUrl($path)
    {
        return substr($path, strlen($this->getPublicDir()));
    }

    public function formatFileSize($size, $precision = 1)
    {
        $unit = 'b';
        if ($size > 1048576) {
            $size = round($size / 1048576, $precision);
            $unit = 'mb';

        } else if ($size > 1024) {
            $size = round($size / 1024, $precision);
            $unit = 'kb';
        }

        return $size . ' ' . $this->translator->trans('media.file.size.' . $unit);
    }

    /**
     * @param $source
     * @return VideoHandlerInterface
     * @throws VideoSourceIsNotSupportedException
     */
    public function getVideoHandler($source)
    {
        return $this->videoResolver->resolve($source);
    }

    /**
     * @param $source
     * @return Model\Video
     * @throws VideoSourceIsNotSupportedException
     */
    public function createVideo($source)
    {
        return $this->getVideoHandler($source)->createVideo($source);
    }
}