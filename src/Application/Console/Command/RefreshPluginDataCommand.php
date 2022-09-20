<?php declare(strict_types=1);

/*
 * This file is part of the 2amigos/take-it-national-api
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\Application\Console\Command;

use App\Domain\Website\WebsiteStatusInterface;
use App\Infrastructure\Console\ColorizedTrait;
use App\Infrastructure\Db\WebsiteRepository;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshPluginDataCommand extends Command
{
    use ColorizedTrait;

    private $output;

    public const GET_PLUGIN_DATA_ENDPOINT = '/wp-json/take-it-national/v1/get-plugin-data/';

    private $websiteRepository;

    public function __construct(
        WebsiteRepository $websiteRepository,
        ?string $name = null
    )
    {
        $this->websiteRepository = $websiteRepository;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('refresh-plugin-data:run');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->setColors($output);

        $websites = $this->websiteRepository->findAll([], null, false);

        if (empty($websites)) {
            $this->output->writeln('<g>No websites to process.</g>');
            return;
        }

        foreach ($websites as $website) {
            if ((int)$website['status'] !== WebsiteStatusInterface::ACTIVE) {
                continue;
            }

            $getPluginDataUrl = $website['url'] . self::GET_PLUGIN_DATA_ENDPOINT;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $getPluginDataUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $response = json_decode($response);
            }

            if (!isset($response->data) || !isset($response->status) || $response->status !== 200) {
                $this->output->writeln("<r>Invalid url provided for website id={$website['id']}</r>");
                try {
                    $this->websiteRepository->update((int)$website['id'], ['plugin_data' => '']);
                } catch (DBALException $e) {
                    $this->output->writeln("<r>" . "Error updating website id={$website['id']}: " . $e->getMessage() . "</r>");
                }
                continue;
            }

            try {
                $this->websiteRepository->update((int)$website['id'], ['plugin_data' => json_encode($response->data)]);
                $this->output->writeln("<g>Plugin data updated for website id={$website['id']}</g>");
            } catch (DBALException $e) {
                $this->output->writeln("<r>" . "Error updating website id={$website['id']}: " . $e->getMessage() . "</r>");
            }
        }
    }
}
