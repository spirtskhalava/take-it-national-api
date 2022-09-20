<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Console\Command;

use App\Infrastructure\Console\ColorizedTrait;
use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Db\WebsiteRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateUserCommand extends Command
{
    use ColorizedTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var WebsiteRepository
     */
    private $websiteRepository;

    /**
     * CreateUserCommand constructor.
     * @param UserRepository $userRepository
     * @param WebsiteRepository $websiteRepository
     * @param string|null $name
     */
    public function __construct(UserRepository $userRepository, WebsiteRepository $websiteRepository, string $name = null)
    {
        $this->userRepository = $userRepository;
        $this->websiteRepository = $websiteRepository;
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('create-user:run')
            ->setDescription('Create demo user command')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> run command:
<comment>Run</comment>
    <info>php %command.full_name% --username=email@example.com --email=email@example.com --password=password --create-demo-site=1</info>
EOF
            )
            ->addOption('username', 'usr', InputOption::VALUE_REQUIRED, 'Username?')
            ->addOption('email', 'em', InputOption::VALUE_REQUIRED, 'Email?')
            ->addOption('password', 'pw', InputOption::VALUE_REQUIRED, 'Password?')
            ->addOption('role', 'rl', InputOption::VALUE_OPTIONAL, 'Role? If "client" will create demo site', 'admin');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \LogicException
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setColors($output);

        // in case you wish to use file logs

        $loggerName = 'create-user-command';
        $logger = new Logger($loggerName);
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $logger->pushHandler(new StreamHandler('php://stderr', Logger::WARNING));

        $output->writeln('<g>*************** Create user command ***************</g>');

        $userData = [
            'username' => $input->getOption('username'),
            'email' => $input->getOption('email'),
            'password' => password_hash($input->getOption('password'), PASSWORD_DEFAULT, ['cost' => 10]),
            'role' => $input->getOption('role')
        ];

        $userData['id'] = $this->userRepository->insert($userData);
        $userData['raw_password'] = $input->getOption('password');
        $output->writeln('<b> create user command finished:</b>');
        $output->writeln('<b>User</b>');
        $table = new Table($output);
        $table
            ->setHeaders(array_keys($userData))
            ->setRows([$userData])
            ->render();

        if ('client' === $input->getOption('role')) {
            $websiteData = [
                'user_id' => $userData['id'],
                'name' => 'demo-site',
                'url' => 'www.demo-site.com',
                'client_id' => 'demo-site',
                'client_secret' => $userData['password']
            ];

            $websiteData['id'] = $this->websiteRepository->insert($websiteData);
            $websiteData['raw_password'] = $userData['raw_password'];
            $output->writeln('<b>Website</b>');
            $table = new Table($output);
            $table
                ->setHeaders(array_keys($websiteData))
                ->setRows([$websiteData])
                ->render();
        }
    }
}
