<?php

/*
 * This file is part of the slack-cli package.
 *
 * (c) Cas Leentfaar <setactive@casleentfaar.com>
 *
 * For the full copyright and license setactivermation, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\SlackCli\Command;

use CL\Slack\Payload\UsersSetActivePayload;
use CL\Slack\Payload\UsersSetActivePayloadResponse;
use CL\Slack\Payload\PayloadResponseInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cas Leentfaar <setactive@casleentfaar.com>
 */
class UsersSetActiveCommand extends AbstractApiCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('users:set-active');
        $this->setDescription('Lets the slack messaging server know that the token\'s user is currently active');
        $this->setHelp(<<<EOT
The <info>users:set-active</info> command lets the slack messaging server know that the token's
user is currently active. Consult the presence documentation for more details (see link below).

For more information about the related API method, check out the official documentation:
<comment>https://api.slack.com/methods/users.setActive</comment>
EOT
        );
    }

    /**
     * @param InputInterface $input
     *
     * @return UsersSetActivePayload
     */
    protected function createPayload(InputInterface $input)
    {
        $payload = new UsersSetActivePayload();

        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param UsersSetActivePayloadResponse $payloadResponse
     * @param InputInterface                $input
     * @param OutputInterface               $output
     */
    protected function handleResponse(PayloadResponseInterface $payloadResponse, InputInterface $input, OutputInterface $output)
    {
        if ($payloadResponse->isOk()) {
            $this->writeOk($output, 'Successfully informed Slack of the token user\'s active status');
        } else {
            $this->writeError($output, sprintf(
                'Failed to set the user to active: %s',
                lcfirst($payloadResponse->getErrorExplanation())
            ));
        }
    }
}
