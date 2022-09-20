<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Console\Command;

use App\Infrastructure\Console\ColorizedTrait;
use App\Infrastructure\Db\FunnelElementAttributeRepository;
use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Db\UserRepository;
use App\Infrastructure\Db\WebsiteRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportClientFunnelsCommand extends Command
{
    use ColorizedTrait;

    private $userRepository;
    private $websiteRepository;
    private $funnelRepository;
    private $funnelElementRepository;
    private $funnelElementTypeRepository;
    private $funnelElementAttributeRepository;
    private $funnelElementTypeAttributeRepository;
    private $output;

    public function __construct(
        UserRepository $userRepository,
        WebsiteRepository $websiteRepository,
        FunnelRepository $funnelRepository,
        FunnelElementRepository $funnelElementRepository,
        FunnelElementTypeRepository $funnelElementTypeRepository,
        FunnelElementTypeAttributeRepository $funnelElementTypeAttributeRepository,
        FunnelElementAttributeRepository $funnelElementAttributeRepository,
        string $name = null
    ) {
        $this->userRepository = $userRepository;
        $this->websiteRepository = $websiteRepository;
        $this->funnelRepository = $funnelRepository;
        $this->funnelElementRepository = $funnelElementRepository;
        $this->funnelElementTypeRepository = $funnelElementTypeRepository;
        $this->funnelElementAttributeRepository = $funnelElementAttributeRepository;
        $this->funnelElementTypeAttributeRepository = $funnelElementTypeAttributeRepository;

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
            ->setName('import-client-funnels:run')
            ->setDescription('Import client funnels command')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> run command:
<comment>Run</comment>
EOF
            )
            ->addOption('filename', 'fn', InputOption::VALUE_REQUIRED, 'File name? -i.e. walkinlabs.php');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setColors($output);

        $this->output = $output;

        $loggerName = 'import-client-funnels-command';
        $logger = new Logger($loggerName);
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $logger->pushHandler(new StreamHandler('php://stderr', Logger::WARNING));

        $output->writeln('<g>*************** Import Client Funnels command ***************</g>');

        $path = 'config/clients/';
        $filename = $input->getOption('filename');

        if (!file_exists($path . $filename)) {
            $output->writeln('<r>Unknown configuration file</r>');
            die();
        }

        $path .= $filename;

        $output->writeln('<b>Importing ' . $path . '...</b>');

        $data = [];
        $config = require $path;

        $connection = $this->userRepository->getDb();
        try {
            $connection->beginTransaction();
            // create client
            $data['client'] = $config['client']['fields'];
            $output->writeln('<b>Creating client ' . $data['client']['username'] . '...</b>');
            $data['client']['password'] = password_hash($data['client']['password'], PASSWORD_DEFAULT, ['cost' => 10]);
            $data['client']['id'] = $this->userRepository->insert($data['client']);

            // create websites
            foreach ((array)$config['websites'] as $website) {
                $output->writeln('<b>Creating website ' . $website['fields']['name'] . '...</b>');
                $websiteData = $website['fields'];
                $websiteData['user_id'] = $data['client']['id'];
                $websiteData['client_secret'] = password_hash($websiteData['client_secret'], PASSWORD_DEFAULT, ['cost' => 10]);
                $websiteData['id'] = $this->websiteRepository->insert($websiteData);
                $data['websites'][$websiteData['id']] = $websiteData;
                $data['websites'][$websiteData['id']]['funnel'] = $this
                    ->insertFunnel($websiteData['id'], $website['funnel']);
            }
            $connection->commit();
        } catch (\Exception $exception) {
            throw $exception;
            $output->writeln('<r>Error: ' . $exception->getMessage() . '</r>');
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
        }

        $output->writeln('<g>Import Client Funnels command finished!</g>');
    }

    /**
     * @param int $websiteId
     * @param array $config
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    private function insertFunnel(int $websiteId, array $config): array
    {
        $this->output->writeln('<g>Importing funnel ' . $config['fields']['name'] . '...</g>');

        $funnelData = $config['fields'];
        $funnelData['website_id'] = $websiteId;
        $funnelData['id'] = $this->funnelRepository->insert($funnelData);

        // insert element types
        $this->output->writeln('<g>Importing funnel element types...</g>');
        foreach ($config['types'] as $elementType) {
            $elementTypeData = $elementType['fields'];
            $name = strtolower($elementTypeData['name']); // ensure lowercase
            $this->output->writeln('<b>Importing funnel element type  ' . $name . '...</b>');
            $elementTypeData['name'] = $name;
            $elementTypeData['funnel_id'] = $funnelData['id'];
            $funnelData['types'][$name] = $elementTypeData;
            $funnelData['types'][$name]['id'] = $this->funnelElementTypeRepository->insert($elementTypeData);

            // insert element type attributes if any
            if (isset($elementType['attributes']) && !empty($elementType['attributes'])) {
                $funnelData['types'][$name]['attributes'] = $this
                    ->insertTypeAttributes($funnelData['types'][$name]['id'], $elementType['attributes']);
            }
        }

        // insert elements
        $funnelData['elements'] = $this->insertElements($funnelData, $config['elements']);

        return $funnelData;
    }

    private function insertElements(array $funnelData, array $elements, int $parentId = null, bool $root = true): array
    {
        $this->output->writeln(
            sprintf(
                '<g>Importing funnel elements%s...</g>',
                null === $parentId ? '' : ' for parent element id: ' . $parentId
            )
        );
        $elementsData = [];
        foreach ($elements as $type => $element) {
            if ($root === true && false === $element['root']) {
                continue;
            }
            foreach ($element['items'] as $item) {
                $name = is_array($item) ? $item['name'] : $item;
                $elementData = [];
                $elementData['name'] = $name;
                $this->output->writeln('<b>-----------------------------------------</b>');
                $this->output->writeln('<b>Importing funnel element ' . $name . '...</b>');
                $elementData['funnel_id'] = $funnelData['id'];
                $elementData['funnel_element_type_id'] = $funnelData['types'][$type]['id'];
                if (null !== $parentId) {
                    $elementData['parent_element_id'] = $parentId;
                }
                $elementData['id'] = $this->funnelElementRepository->insert($elementData);
                if ($element['child'] ?? false) {
                    if (!isset($elements[$element['child']])) {
                        $this->output(
                            sprintf(
                                '<r> Element child %s for %s does not exists in configuration </r>',
                                $element['child'],
                                $name
                            )
                        );
                    }
                    $elementData['children'] = $this->insertElements(
                        $funnelData,
                        [
                            $element['child'] => $elements[$element['child']],
                        ],
                        $elementData['id'],
                        false
                    );
                }

                if (is_array($item) && isset($item['attributes'])) {
                    // TODO: insert attributes
                }
                $elementsData[] = $elementData;
            }
        }

        return $elementsData;
    }

    private function insertTypeAttributes(int $typeId, array $config): array
    {
        $attributesData = [];

        foreach ($config as $attribute) {
            $attributeData = $attribute['fields'];
            $attributeData['funnel_element_type_id'] = $typeId;
            $attributeData['id'] = $this->funnelElementTypeAttributeRepository->insert($attributesData);
            $attributesData[] = $attributeData;
        }

        return $attributesData;
    }
}
