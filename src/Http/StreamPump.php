<?php

namespace Undine\Http;

class StreamPump
{
    /**
     * @var OutputFlusher
     */
    private $outputFlusher;

    /**
     * @var int|null
     */
    private $index;

    /**
     * @param OutputFlusher $outputFlusher
     * @param int|null      $index
     */
    public function __construct(OutputFlusher $outputFlusher, $index = null)
    {
        if ($index !== null && !is_int($index)) {
            throw new \InvalidArgumentException(sprintf('The $index parameter must be null or an integer, %s given.', gettype($index)));
        }

        $this->outputFlusher = $outputFlusher;
        $this->index = $index;
    }

    /**
     * @param string $progress A valid JSON, a JsonSerializable or an array.
     * @param bool   $final    Whether this is a final response and not a progress message.
     */
    public function __invoke($progress, $final = false)
    {
        $key = $final ? 'result' : 'progress';

        if (is_string($progress)) {
            // Strings are expected to be valid JSON.
            if ($this->index !== null) {
                $this->outputFlusher->flushMessage(sprintf('{"index":%d,"%s":%s}', $this->index, $key, $progress));
            } else {
                $this->outputFlusher->flushMessage($progress);
            }
        } elseif ($progress instanceof \JsonSerializable || is_array($progress)) {
            if ($this->index !== null) {
                $progress = ['index' => $this->index, $key => $progress];
            }
            $this->outputFlusher->flushMessage(json_encode($progress));
        } else {
            throw new \RuntimeException(sprintf('$progress must be valid JSON, an array or a JsonSerializable; got %s.', gettype($progress)));
        }
    }
}
