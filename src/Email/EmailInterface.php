<?php

namespace Undine\Email;

/**
 * Every application email must implement this interface. Its job is to create full email message,
 * including subject, body, receivers, attachments and all the markup.
 */
interface EmailInterface
{
    /**
     * The method MUST throw an exception if provided parameters do not comply with requirements 100%.
     *
     * @param array $parameters
     *
     * @return \Swift_Message
     *
     * @throws \Exception
     */
    public function createMessage(array $parameters = []);
}
