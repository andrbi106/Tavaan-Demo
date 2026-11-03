<?php

declare(strict_types=1);

namespace Tavaan\Engine\Mail\Contracts;

/**
 * قرارداد Transport.
 */
interface TransportInterface
{
    public function send(\Tavaan\Engine\Mail\Message $message): \Tavaan\Engine\Mail\SentMessage;
}
