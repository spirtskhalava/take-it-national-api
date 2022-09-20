<?php
declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Funnel;

use App\Domain\Element\FunnelElementStatusInterface;
use App\Domain\Funnel\FunnelStatusInterface;
use App\Domain\TypeAttribute\FunnelElementTypeAttributeStatusInterface;
use App\Domain\Website\WebsiteStatusInterface;
use App\Infrastructure\Db\FunnelElementAttributeRepository;
use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeAttributeRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use App\Infrastructure\Db\FunnelRepository;
use App\Infrastructure\Db\WebsiteRepository;

final class FunnelRefreshConfigurationHandler
{
    private $websiteRepository;
    private $funnelRepository;
    private $funnelElementRepository;
    private $funnelElementTypeRepository;
    private $funnelElementTypeAttributeRepository;
    private $funnelElementAttributeRepository;

    private $typesById = [];
    private $elementAttributesCollection = [];
    private $funnelTypeAttributes = [];

    /**
     * FunnelRefreshConfigurationHandler constructor.
     * @param WebsiteRepository $websiteRepository
     * @param FunnelRepository $funnelRepository
     * @param FunnelElementTypeRepository $funnelElementTypeRepository
     * @param FunnelElementTypeAttributeRepository $funnelElementTypeAttributeRepository
     * @param FunnelElementRepository $funnelElementRepository
     * @param FunnelElementAttributeRepository $funnelElementAttributeRepository
     */
    public function __construct(
        WebsiteRepository $websiteRepository,
        FunnelRepository $funnelRepository,
        FunnelElementTypeRepository $funnelElementTypeRepository,
        FunnelElementTypeAttributeRepository $funnelElementTypeAttributeRepository,
        FunnelElementRepository $funnelElementRepository,
        FunnelElementAttributeRepository $funnelElementAttributeRepository
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->funnelRepository = $funnelRepository;
        $this->funnelElementTypeRepository = $funnelElementTypeRepository;
        $this->funnelElementTypeAttributeRepository = $funnelElementTypeAttributeRepository;
        $this->funnelElementRepository = $funnelElementRepository;
        $this->funnelElementAttributeRepository = $funnelElementAttributeRepository;
    }

    /**
     * @param FunnelRefreshConfigurationCommand $command
     * @throws FunnelRefreshConfigurationException
     */
    public function handle(FunnelRefreshConfigurationCommand $command): void
    {
        try {
            $websites = $this->websiteRepository->findAll([], $command->getToken(), false);
            if (!empty($websites)) {
                foreach ($websites as $website) {
                    if ((int)$website['status'] === WebsiteStatusInterface::ACTIVE) {
                        $this->processFunnelsForWebsite($website);
                    }
                }
            }
        } catch (\Exception $exception) {
            throw new FunnelRefreshConfigurationException();
        }
    }

    /**
     * @param $website
     * @throws \Doctrine\DBAL\DBALException
     */
    private function processFunnelsForWebsite($website)
    {
        $funnels = $this->funnelRepository->findAllByWebsiteId((int)$website['id'], [], FunnelStatusInterface::ACTIVE, false);

        if (!empty($funnels)) {
            foreach ($funnels as $funnel) {
                $this->processFunnel($funnel);
            }
        }
    }

    /**
     * @param $funnel
     * @throws \Doctrine\DBAL\DBALException
     */
    public function processFunnel($funnel): void
    {
        $types = $this->funnelElementTypeRepository->findAllByFunnelId((int)$funnel['id'], [], false);

        $structureType = [];
        $toFill = [];
        $this->typesById = [];

        if (empty($types)) {
            return;
        }

        foreach ($types as $type) {
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

    /**
     * @param $funnelElement
     * @return array
     */
    private function getElementAttributes($funnelElement): array
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

    /**
     * @param $element
     * @param $collection
     * @param $depth
     * @return array
     */
    private function getChildren($element, $collection, $depth): array
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
}
