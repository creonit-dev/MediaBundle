<?php


namespace Creonit\MediaBundle\VideoHandler;


use Creonit\MediaBundle\Exception\VideoSourceIsNotSupportedException;

class VideoResolver
{
    /**
     * @var VideoHandlerInterface[]
     */
    protected $handlers = [];

    /**
     * @param VideoHandlerInterface $handler
     */
    public function addHandler(VideoHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param $source
     * @return VideoHandlerInterface
     * @throws VideoSourceIsNotSupportedException
     */
    public function resolve($source)
    {
        foreach ($this->handlers as $handler) {
            if (true === $handler->supports($source)) {
                return $handler;
            }
        }

        $exception = new VideoSourceIsNotSupportedException();
        $exception->setSource($source);
        throw $exception;
    }
}