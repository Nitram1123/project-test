<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MemberRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:member:change-password',
    description: 'Change the password of a member.',
)]
class MemberChangePasswordCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private MemberRepository $memberRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password')
            ->setHelp(\implode("\n", [
                'The <info>app:member:change-password</info> command changes the password of a member:',
                '<info>php %command.username% Martin</info>',
                'This interactive shell will first ask you for a password.',
                'You can alternatively specify the password as a second argument:',
                '<info>php %command.username% Martin change_me</info>',
            ]));
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (! $input->getArgument('username')) {
            $question = new Question('Please give the username:');
            $question->setValidator(function ($username) {
                if ($username === '') {
                    throw new \Exception('Username can not be empty');
                }

                if (! $this->memberRepository->findOneBy(['username' => $username])) {
                    throw new \Exception('No member found with this username');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if (! $input->getArgument('password')) {
            $question = new Question('Please enter the new password:');
            $question->setValidator(static function ($password) {
                if ($password === '') {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $helper = $this->getHelper('question');
            \assert($helper instanceof QuestionHelper);
            $answer = $helper->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $member   = $this->memberRepository->findOneBy(['username' => $username]);

        $newHashedPassword = $this->userPasswordHasher->hashPassword(
            $member,
            $input->getArgument('password')
        );

        $this->memberRepository->upgradePassword($member, $newHashedPassword);

        $io->success(\sprintf('Changed password for member %s.', $username));

        return 0;
    }
}
