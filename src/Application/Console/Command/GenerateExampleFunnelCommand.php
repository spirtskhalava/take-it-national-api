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
use App\Infrastructure\Db\WebsiteRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateExampleFunnelCommand extends Command
{
    use ColorizedTrait;

    private $funnelRepository;
    private $userRepository;
    private $websiteRepository;
    private $funnelElementRepository;
    private $funnelElementTypeRepository;
    private $funnelElementAttributeRepository;
    private $funnelElementTypeAttributeRepository;

    private $typesById = [];
    private $elementAttributesCollection = [];
    private $funnelTypeAttributes = [];
    private $output;

    public function __construct(
        WebsiteRepository $websiteRepository,
        FunnelRepository $funnelRepository,
        FunnelElementTypeRepository $funnelElementTypeRepository,
        FunnelElementTypeAttributeRepository $funnelElementTypeAttributeRepository,
        FunnelElementRepository $funnelElementRepository,
        FunnelElementAttributeRepository $funnelElementAttributeRepository,
        string $name = null
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->funnelRepository = $funnelRepository;
        $this->funnelElementTypeRepository = $funnelElementTypeRepository;
        $this->funnelElementTypeAttributeRepository = $funnelElementTypeAttributeRepository;
        $this->funnelElementRepository = $funnelElementRepository;
        $this->funnelElementAttributeRepository = $funnelElementAttributeRepository;
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
            ->setName('generate-example-funnel:run')
            ->setDescription('Refresh customers configuration command')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> run command:
<comment>Run</comment>
EOF
            )->addOption('name', 'nm', InputOption::VALUE_REQUIRED, 'name?')
            ->addOption('website_id', 'ws', InputOption::VALUE_REQUIRED, 'website_id?');
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
        $name = $input->getOption('name');
        $website_id = $input->getOption('website_id');

        if (empty($name)) {
            $output->writeln('<g>Please provide funnel name</g>');
            return;
        }

        $website = $this->websiteRepository->findOne((int)$website_id);

        if (empty($website)) {
            $output->writeln('<g>Please provide existing website id</g>');
            return;
        }

        $output->writeln('<g>Generating funnel</g>');
        $funnel_id = $this->funnelRepository->insert([
            'name' => $name,
            'website_id' => $website['id']
        ]);

        $output->writeln('<g>Generating funnel element types</g>');
        $type_keyword_id = $this->funnelElementTypeRepository->insert([
            'funnel_id' => $funnel_id,
            'name' => 'keyword',
            'url_pattern' => '[keyword]',
            'title' => 'Keyword [keyword]',
            'description' => '[keyword] is the best',
        ]);

        $type_city_id = $this->funnelElementTypeRepository->insert([
            'funnel_id' => $funnel_id,
            'name' => 'city',
            'url_pattern' => '[keyword]-in-[city]',
            'title' => 'Keyword [city]',
            'description' => '[city] is the best',
        ]);

        $type_attribute_id = $this->funnelElementTypeAttributeRepository->insert([
            'funnel_element_type_id' => $type_city_id,
            'name' => 'district1'
        ]);

        $type_second_attribute_id = $this->funnelElementTypeAttributeRepository->insert([
            'funnel_element_type_id' => $type_city_id,
            'name' => 'district2'
        ]);

        $output->writeln('<g>Generating funnel elements</g>');
        $id = $this->funnelElementRepository->insert([
            'name' => 'first_keyword',
            'funnel_id' => $funnel_id,
            'funnel_element_type_id' => $type_keyword_id,
        ]);

        $id = $this->funnelElementRepository->insert([
            'name' => 'second_keyword',
            'funnel_id' => $funnel_id,
            'funnel_element_type_id' => $type_keyword_id,
        ]);

        // add element
        $city_id = $this->funnelElementRepository->insert([
            'name' => 'miami',
            'funnel_id' => $funnel_id,
            'parent_element_id' => $id,
            'funnel_element_type_id' => $type_city_id,
        ]);

        $this->funnelElementAttributeRepository->insert([
            'funnel_element_id' => $city_id,
            'funnel_element_type_attribute_id' => $type_attribute_id,
            'value' => 'south miami'
        ]);
        $this->funnelElementAttributeRepository->insert([
            'funnel_element_id' => $city_id,
            'funnel_element_type_attribute_id' => $type_second_attribute_id,
            'value' => 'north miami'
        ]);

        // add second element
        $city_id = $this->funnelElementRepository->insert([
            'name' => 'New York',
            'funnel_id' => $funnel_id,
            'parent_element_id' => $id,
            'funnel_element_type_id' => $type_city_id,
        ]);

        $this->funnelElementAttributeRepository->insert([
            'funnel_element_id' => $city_id,
            'funnel_element_type_attribute_id' => $type_attribute_id,
            'value' => 'south new york'
        ]);
        $this->funnelElementAttributeRepository->insert([
            'funnel_element_id' => $city_id,
            'funnel_element_type_attribute_id' => $type_second_attribute_id,
            'value' => 'north new york'
        ]);

        $output->writeln('<g>Done!</g>');
    }
}
