<?php

namespace App\Command;

use App\Service\ModerationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test-sms')]
class TestSmsCommand extends Command
{
    public function __construct(private ModerationService $moderationService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Testing SMS moderation...');
        
        try {
            $result = $this->moderationService->checkContent(
                'This contains kalma khayba word',
                'Test Artist',
                'Test Journal'
            );
            
            if ($result) {
                $output->writeln('✅ Bad word detected and SMS should be sent');
            } else {
                $output->writeln('❌ Bad word not detected');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}