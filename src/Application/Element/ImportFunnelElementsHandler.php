<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Element;

use App\Domain\Element\FunnelElementStatusInterface;
use App\Infrastructure\Db\FunnelElementRepository;
use App\Infrastructure\Db\FunnelElementTypeRepository;
use League\Flysystem\Filesystem;
use PhpOffice\PhpSpreadsheet\Exception;
use Slim\Http\UploadedFile;

final class ImportFunnelElementsHandler
{
    /**
     * @var FunnelElementRepository
     */
    private $funnelElementRepository;
    /**
     * @var FunnelElementTypeRepository
     */
    private $funnelElementTypeRepository;
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * ImportFunnelElementsHandler constructor.
     * @param FunnelElementRepository $funnelElementRepository
     * @param FunnelElementTypeRepository $funnelElementTypeRepository
     * @param Filesystem $fs
     */
    public function __construct(FunnelElementRepository $funnelElementRepository, FunnelElementTypeRepository $funnelElementTypeRepository, Filesystem $fs)
    {
        $this->funnelElementRepository = $funnelElementRepository;
        $this->funnelElementTypeRepository = $funnelElementTypeRepository;
        $this->fs = $fs;
    }

    /**
     * @param ImportFunnelElementsCommand $command
     * @return string
     * @throws \Exception
     */
    public function handle(ImportFunnelElementsCommand $command): string
    {
        set_time_limit(180);

        try {
            $config = $command->getConfig();

            $uploadDir = 'import/upload/';
            $filePath = $this->moveUploadedFile($uploadDir, $config['file']);
            $rows = $this->getDataFromFile($filePath);
            unlink($filePath);

            $commandData = $command->getData();
            $requestParams = [
                'funnelId' => (int)$commandData['funnelId'],
                'elementId' => (int)$commandData['elementId'],
                'isImportForElementSiblings' => (int)$commandData['isImportForElementSiblings']
            ];

            $isImportOfRootElements = empty($requestParams['elementId']);
            $importCount = 0;

            $funnelElementTypes = $this->funnelElementTypeRepository->findAllByFunnelId($requestParams['funnelId']);
            $orderedFunnelElementTypeIds = [];

            foreach ($funnelElementTypes as $type) {
                if ($type['has_child'] === '0' && $type['status'] === '1') {
                    $orderedFunnelElementTypeIds = explode('/', substr($type['ancestry'], 1));
                    $orderedFunnelElementTypeIds[] = $type['id'];
                }
            }

            $importRootTypeId = $isImportOfRootElements
                ? $this->getRootElementTypeId($requestParams['funnelId'])
                : $this->getChildElementTypeId($requestParams['funnelId'], $requestParams['elementId']);

            $importRootTypeIndex = array_search($importRootTypeId, $orderedFunnelElementTypeIds);

            $elements = [];
            $i = 0;

            foreach ($rows as $row) {
                foreach ($row as $cellIndex => $cellValue) {
                    if (empty($cellValue)) {
                        continue;
                    }
                    $funnelElementTypeIndex = $importRootTypeIndex + $cellIndex;
                    $elements[] = [
                        'name' => $cellValue,
                        'level' => $cellIndex,
                        'parent' => $cellIndex === 0 ? 0 : ['name' => $elements[$i - 1]['name'], 'level' => $cellIndex - 1],
                        'funnel_element_type_id' => $orderedFunnelElementTypeIds[$funnelElementTypeIndex] ?? null,
                        'funnel_id' => $requestParams['funnelId']
                    ];
                    $i++;
                }
            }

            $parentElementsIds = [];

            if (!$isImportOfRootElements) {
                if ($requestParams['isImportForElementSiblings']) {
                    $parentElementsTypeId = $this->getElementTypeId($requestParams['elementId']);
                    $parentElements = $this->funnelElementRepository->findAllByTypeId($parentElementsTypeId);
                    $parentElementsIds = array_map(function ($element) {
                        return (int)$element['id'];
                    }, $parentElements);
                } else {
                    $parentElementsIds[] = $requestParams['elementId'];
                }
            }

            $connection = $this->funnelElementRepository->getDb();

            try {
                $connection->beginTransaction();
                if ($isImportOfRootElements) {
                    $this->deleteExistingRootElements($requestParams['funnelId']);
                    $importCount = $this->importElements($elements);
                } else {
                    foreach ($parentElementsIds as $parentElementId) {
                        $this->deleteExistingChildElements($parentElementId);
                    }
                    $importCount = $this->importElements($elements, $parentElementsIds);
                }

                $connection->commit();
            } catch (\Exception $exception) {
                if ($connection->isTransactionActive()) {
                    $connection->rollBack();
                    throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
                }
            }

        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        $parentElementsCount = count($parentElementsIds);

        return (
        $isImportOfRootElements
            ? "Successfully imported {$importCount} root elements!"
            : "Successfully imported {$importCount} children elements" .
            (
            $parentElementsCount > 1
                ? " for all {$parentElementsCount} elements of the same type!"
                : '!'
            )
        );
    }

    /**
     * @param $elements
     * @param array $parentElementIds
     * @return int importCount
     * @throws \Exception
     */
    public function importElements($elements, $parentElementIds = []): int
    {
        $importedElements = [];

        try {
            foreach ($elements as $index => $element) {
                if ($this->multiArraySearch($importedElements, $element)) {
                    continue;
                }
                if (empty($element['parent'])) {
                    $parentId = null;
                } else {
                    $keysFound = $this->multiArraySearch($importedElements, $element['parent']);
                    $parentKey = $keysFound[0] ?? null;
                    $parentId = $importedElements[$parentKey]['id'] ?? null;
                }
                $data = [
                    'name' => trim($element['name']),
                    'funnel_element_type_id' => $element['funnel_element_type_id'],
                    'funnel_id' => $element['funnel_id'],
                    'parent_id' => $parentId
                ];
                $element['id'] = $this->funnelElementRepository->insert($data);
                if (empty($element['parent'])) {
                    foreach ($parentElementIds as $parentElementId) {
                        $this->funnelElementRepository->assign($parentElementId, $element['id']);
                    }
                }
                $importedElements[] = $element;
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        return count($importedElements);
    }

    /**
     * Multi-array search
     *
     * @param array $array
     * @param array $params
     * @return array
     */
    function multiArraySearch($array, $params)
    {
        $result = [];
        // Iterate over each array element
        foreach ($array as $key => $value) {
            // Iterate over each search condition
            foreach ($params as $k => $v) {
                // If the array element does not meet the search condition then continue to the next element
                if (!isset($value[$k]) || $value[$k] != $v) {
                    continue 2;
                }
            }
            // Add the array element's key to the result array
            $result[] = $key;
        }

        // Return the result array
        return $result;
    }

    /**
     * @param int $elementId
     * @return int
     * @throws \Exception
     */
    public function getElementTypeId(int $elementId): int
    {
        try {
            $element = $this->funnelElementRepository->findOneById($elementId);
            return isset($element['funnel_element_type_id']) ? (int)$element['funnel_element_type_id'] : 0;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * @param int $funnelId
     * @return int rootElementTypeId
     * @throws \Exception
     */
    public function getRootElementTypeId(int $funnelId): int
    {
        try {
            $rootElementType = $this->funnelElementTypeRepository->findOneActiveRootByFunnelId($funnelId);
            return isset($rootElementType['id']) ? (int)$rootElementType['id'] : 0;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * @param int $funnelId
     * @param int $parentElementId
     * @return int
     * @throws \Exception
     */
    public function getChildElementTypeId(int $funnelId, int $parentElementId): int
    {
        try {
            $parentElement = $this->funnelElementRepository->findOneById($parentElementId);
            $funnelElementTypes = $this->funnelElementTypeRepository->findAllByFunnelId($funnelId);
            $key = array_search($parentElement['funnel_element_type_id'], array_column($funnelElementTypes, 'parent_type_id'));
            return isset($funnelElementTypes[$key]) ? (int)$funnelElementTypes[$key]['id'] : 0;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * @param int $elementId
     * @throws \Exception
     */
    public function deleteExistingChildElements(int $elementId): void
    {
        try {
            $childElements = $this->funnelElementRepository->findAllChildElementsByElementId($elementId);
            foreach ($childElements as $index => $childElement) {
                $this->funnelElementRepository->delete((int)$childElement['id']);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * @param int $funnelId
     * @throws \Exception
     */
    public function deleteExistingRootElements(int $funnelId): void
    {
        try {
            $rootElements = $this->funnelElementRepository->findAllRootByFunnelId($funnelId, [], FunnelElementStatusInterface::ACTIVE, false);
            foreach ($rootElements as $index => $rootElement) {
                $this->funnelElementRepository->delete((int)$rootElement['id']);
            }
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * @param $filePath
     * @return array
     * @throws \Exception
     */
    public function getDataFromFile(string $filePath): array
    {
        try {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $rows = $spreadsheet->getActiveSheet()->toArray();
        } catch (Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }

        return $rows;
    }

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory directory to which the file is moved
     * @param UploadedFile $uploadedFile file uploaded file to move
     * @return string path of moved file
     * @throws \Exception
     */
    public function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string
    {
        if (!is_dir($directory)) {
            // dir doesn't exist, make it
            mkdir($directory, 0777, true);
        }

        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $baseName = bin2hex(random_bytes(16));
        $fileName = sprintf('%s.%0.8s', $baseName, $extension);

        $filePath = $directory . $fileName;
        $uploadedFile->moveTo($filePath);

        return $filePath;
    }
}
