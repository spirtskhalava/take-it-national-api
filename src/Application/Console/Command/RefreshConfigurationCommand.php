<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Console\Command;

use App\Domain\Element\FunnelElementStatusInterface;
use App\Domain\Funnel\FunnelStatusInterface;
use App\Domain\TypeAttribute\FunnelElementTypeAttributeStatusInterface;
use App\Domain\Website\WebsiteStatusInterface;
use App\Infrastructure\Console\ColorizedTrait;
use App\Infrastructure\Db\FunnelElementAttributeRepository;
use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Db\WebsiteRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RefreshConfigurationCommand extends Command
{
    use ColorizedTrait;

    private $websiteRepository;
    private $funnelRepository;
    private $funnelElementRepository;
    private $funnelElementTypeRepository;
    private $funnelElementTypeAttributeRepository;
    private $funnelElementAttributeRepository;

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

    public function processFunnel($funnel): void
    {
        $types = $this->funnelElementTypeRepository->findAllByFunnelId((int)$funnel['id'], [], false);

        $structureType = [];
        $toFill = [];
        $this->typesById = [];

        if (empty($types)) {
            $this->output->writeln('<g>Funnel ' . $funnel['name'] . ' has no defined types.</g>');
            return;
        }

        foreach ($types as $type) {
            $attrResult = [];
            $toFill[$type['name']] = $type['id'];

            $structureType[$type['name']] = [
                'urlPattern' => $type['url_pattern'],
                'metaTitlePattern' => $type['title'],
                'metaDescriptionPattern' => $type['description'],
                'attributes' => []
            ];
        }

        $this->funnelTypeAttributes = [];
        $typeAttributes = $this->funnelElementTypeAttributeRepository->findAllByFunnelId(
            (int)$funnel['id'],
            [],
            FunnelElementTypeAttributeStatusInterface::ACTIVE,
            false
        );

        foreach ($typeAttributes as $attribute) {
            $this->funnelTypeAttributes[$attribute['id']] = $attribute['name'];

            foreach ($structureType as $key => $value) {
                if ($attribute['funnel_element_type_id'] !== $toFill[$key]) {
                    continue;
                }

                $structureType[$key]['attributes'][] = $attribute['name'];
            }
        }

        $elements = $this->funnelElementRepository->findAllByFunnelId((int)$funnel['id'], [], FunnelElementStatusInterface::ACTIVE, false);
        $collection = [];

        // just bypass table gateway limitations
        foreach ($elements as $element) {
            $collection[] = (object)[
                'id' => $element['id'],
                'name' => $element['name'],
                'type_name' => strtolower($element['type_name']),
                'parent_element_id' => $element['parent_element_id'] ?? null,
                'funnel_element_type_id' => $element['funnel_element_type_id']
            ];
        }

        $elements = null;
        $structureElements = [];
        $this->elementAttributesCollection = [];
        $elementAttributes = $this->funnelElementAttributeRepository->findAllByFunnelId((int)$funnel['id']);

        foreach ($elementAttributes as $attribute) {
            $this->elementAttributesCollection[] = [
                'id' => $attribute['id'],
                'value' => $attribute['value'],
                'name' => $attribute['attribute_name'],
                'funnel_element_type_attribute_id' => $attribute['funnel_element_type_attribute_id'],
                'funnel_element_id' => $attribute['funnel_element_id']
            ];
        }

        foreach ($collection as $key => $element) {
            if (!empty($element->parent_element_id)) {
                continue;
            }
            unset($collection[$key]);

            $row = $this->getElementAttributes($element);
            $row['name'] = $element->name;
            $row['type'] = $element->type_name;
            $row['children'] = $this->getChildren($element, $collection, 1);
            $structureElements[] = $row;
        }

        $funnel['cached_structure'] = json_encode([
            'config' => $structureElements,
            'types' => $structureType
        ]);

        $this->funnelRepository->update((int)$funnel['id'], $funnel);
    }

    public function getElementAttributes($funnelElement)
    {
        $forbiddenNames = ['name', 'children', 'type'];
        $result = [];

        foreach ($this->elementAttributesCollection as $attribute) {
            if ($attribute['funnel_element_id'] === $funnelElement->id) {
                $attrName = $attribute['name'];
                if (\in_array($attrName, $forbiddenNames, false)) {
                    $attrName = 'attribute_' . $attrName;
                }

                $result[$attrName] = $attribute['value'];
            }
        }

        return $result;
    }

    public function getChildren($element, $collection, $depth)
    {
        $children = [];
        if ($depth > 12) {
            return [];
        }
        $depth++;

        foreach ($collection as $key => $row) {
            if (empty($row->parent_element_id)) {
                continue;
            }

            unset($collection[$key]);

            if ($row->parent_element_id === $element->id) {
                $currentElement = $this->getElementAttributes($row);
                $currentElement['name'] = $row->name;
                $currentElement['type'] = $row->type_name;
                $currentElement['children'] = $this->getChildren($row, $collection, $depth);
                $children[] = $currentElement;
            }
        }

        return $children;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('refresh-configuration:run')
            ->setDescription('Refresh website funnels configuration command')
            ->setHelp(
                <<<EOF
The <info>%command.name%</info> run command:
<comment>Run</comment>
    <info>php %command.full_name% </info>
EOF
            );
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
        ini_set('memory_limit', '4096M');
        $this->output = $output;
        $this->setColors($output);
        $loggerName = 'create-user-command';
        $logger = new Logger($loggerName);
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));
        $logger->pushHandler(new StreamHandler('php://stderr', Logger::WARNING));
        $output->writeln('<g>*************** Refresh configuration command ***************</g>');

        $websites = $this->websiteRepository->findAll([], null, false);
        if (empty($websites)) {
            $this->output->writeln('<g>No websites to process.</g>');
            return;
        }

        foreach ($websites as $website) {
            if ((int)$website['status'] !== WebsiteStatusInterface::ACTIVE) {
                continue;
            }
            $this->output->writeln(PHP_EOL . '<g>Processing funnels for website: ' . $website['name'] . '</g>');
            $this->processFunnelsForWebsite($website);
        }
    }

    private function processFunnelsForWebsite($website)
    {
        $funnels = $this->funnelRepository->findAllByWebsiteId((int)$website['id'], [], FunnelStatusInterface::ACTIVE, false);
        if (empty($funnels)) {
            $this->output->writeln('<g>Website has no active funnels</g>');
            return;
        }

        foreach ($funnels as $funnel) {
            $this->output->writeln('<g>Processing funnel: ' . $funnel['name'] . '</g>');
            $this->processFunnel($funnel);
        }
    }
}
