<?php

namespace App\Command;

use App\Service\WeatherService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:weather',
    description: 'Gets weather forecast',
)]
class WeatherCommand extends Command
{
    public function __construct( private readonly  WeatherService $ws , string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('lat', InputArgument::REQUIRED, 'latitude')
            ->addArgument('lng', InputArgument::REQUIRED, 'longitude')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $lat = $input->getArgument('lat');
        $lng = $input->getArgument('lng');

        $response  = $this->ws->getWeather($lat, $lng);

        $this->displayForecast($response['daily']);


        return Command::SUCCESS;
    }

    private function displayForecast($daily): void
    {
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table->setHeaders(['Day', 'Temperature Min', 'Temperature Max']);
        $rows = [];
        foreach ($daily['time'] as $key => $date) {
            $rows[] = [
                $date,
                $daily['temperature_2m_min'][$key],
                $daily['temperature_2m_max'][$key]
            ];

        }
        $table->setRows($rows);
        $table->render();
    }
}
