<?php

/*
 * This file is part of the slack-cli package.
 *
 * (c) Cas Leentfaar <info@casleentfaar.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CL\SlackCli\Command;

use CL\Slack\Payload\ChannelsListPayload;
use CL\Slack\Payload\ChannelsListPayloadResponse;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Cas Leentfaar <info@casleentfaar.com>
 */
class ChannelsListCommand extends AbstractApiCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('channels:list');
        $this->setDescription('Returns a list of all channels in your Slack team');
        $this->addOption('exclude-archived', null, InputOption::VALUE_OPTIONAL, 'Don\'t return archived channels.');
        $this->setHelp(<<<EOT
The <info>channels:list</info> command returns a list of all channels in your Slack team.
This includes channels the caller is in, channels they are not currently in, and archived channels.
The number of (non-deactivated) members in each channel is also returned.

For more information about the related API method, check out the official documentation:
<comment>https://api.slack.com/methods/channels.list</comment>
EOT
        );
    }

    /**
     * @return ChannelsListPayload
     */
    protected function createPayload()
    {
        $payload = new ChannelsListPayload();
        $payload->setExcludeArchived($this->input->getOption('exclude-archived'));

        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param ChannelsListPayloadResponse $payloadResponse
     */
    protected function handleResponse($payloadResponse)
    {
        if ($payloadResponse->isOk()) {
            $channels = $payloadResponse->getChannels();
            $this->output->writeln(sprintf('Received <comment>%d</comment> channels...', count($channels)));
            if (!empty($channels)) {
                $rows = [];
                foreach ($payloadResponse->getChannels() as $channel) {
                    $row = $this->serializeObjectToArray($channel);
                    $row['purpose'] = !$channel->getPurpose() ?: $channel->getPurpose()->getValue();
                    $row['topic'] = !$channel->getTopic() ?: $channel->getTopic()->getValue();

                    $rows[] = $row;
                }
                $this->renderTable($rows, null);
            } else {
                $this->writeError('No channels seem to be assigned to your team... this is strange...');
            }
        } else {
            $this->writeError(sprintf('Failed to list channels. %s', lcfirst($payloadResponse->getErrorExplanation())));
        }
    }
}
