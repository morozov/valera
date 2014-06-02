<?php

namespace Valera\Worker;

use Psr\Log\LoggerAwareInterface;

/**
 * Item processing result handler
 */
interface ResultHandler extends LoggerAwareInterface
{
    /**
     * Handles item processing result
     *
     * @param \Valera\Queueable $item
     * @param \Valera\Worker\Result    $result
     */
    public function handle($item, $result);
}
