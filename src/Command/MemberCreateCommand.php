<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:member:new',
    description: 'Create a member.',
)]
class MemberCreateCommand extends Command
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
            ->addArgument('password', InputArgument::REQUIRED, 'The password')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Set the member as super admin')
            ->addOption('super-admin', null, InputOption::VALUE_NONE, 'Set the member as super admin')
            ->setHelp(\implode("\n", [
                'The <info>app:member:create</info> command creates a member:',
                '<info>php %command.full_name% Martin.GILBERT</info>',
                'This interactive shell will ask you for a password.',
                'You can create an admin via the admin flag or a super admin via the super-admin flag:',
                '<info>php %command.full_name% --admin</info>',
            ]));
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $questions = [];

        if (! $input->getArgument('username')) {
            $question = new Question('Please enter the username:');
            $question->setValidator(function ($username) {
                if ($username === '') {
                    throw new \Exception('Username can not be empty');
                }

                if ($this->memberRepository->findOneBy(['username' => $username])) {
                    throw new \Exception('Username is already used');
                }

                return $username;
            });
            $questions['username'] = $question;
        }

        if (! $input->getArgument('password')) {
            $question = new Question('Please choose a password:');
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
        $io     = new SymfonyStyle($input, $output);
        $member = new Member();
        $member
            ->setUsername($input->getArgument('username'))
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $member,
                    $input->getArgument('password')
                )
            );

        if ($input->getOption('admin')) {
            $member->setRoles(['ROLE_ADMIN']);
        }

        if ($input->getOption('super-admin')) {
            $member->setRoles(['ROLE_SUPER_ADMIN']);
        }

        $this->memberRepository->add($member, true);

        $io->success(\sprintf('Created member with username %s.', $member->getUserIdentifier()));

        return 0;
    }
}
