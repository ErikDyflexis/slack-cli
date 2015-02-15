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
use CL\Slack\Payload\PayloadInterface;
use CL\Slack\Payload\PayloadResponseInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @param InputInterface $input
     *
     * @return ChannelsListPayload
     */
    protected function createPayload(InputInterface $input)
    {
        $payload = new ChannelsListPayload();
        $payload->setExcludeArchived($input->getOption('exclude-archived'));
        
        return $payload;
    }

    /**
     * {@inheritdoc}
     *
     * @param ChannelsListPayloadResponse $payloadResponse
     * @param InputInterface              $input
     * @param OutputInterface             $output
     */
    protected function handleResponse(PayloadResponseInterface $payloadResponse, InputInterface $input, OutputInterface $output)
    {
        if ($payloadResponse->isOk()) {
            $channels = $payloadResponse->getChannels();
            $output->writeln(sprintf('Received <comment>%d</comment> channels...', count($channels)));
            if (!empty($channels)) {
                $rows = [];
                foreach ($payloadResponse->getChannels() as $channel) {
                    $row = $this->serializeObjectToArray($channel);
                    $row['purpose'] = !$channel->getPurpose() ?: $channel->getPurpose()->getValue();
                    $row['topic'] = !$channel->getTopic() ?: $channel->getTopic()->getValue();

                    $rows[] = $row;
                }
                $this->renderTable($output, $rows, null);
                $this->writeOk($output, 'Successfully listed channels');
            } else {
                $this->writeError($output, 'No channels seem to be assigned to your team... this is strange...');
            }
        } else {
            $this->writeError($output, sprintf('Failed to list channels. %s', lcfirst($payloadResponse->getErrorExplanation())));
        }
    }
}
