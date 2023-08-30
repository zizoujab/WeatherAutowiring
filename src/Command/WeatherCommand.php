<?php

namespace App\Command;

use App\Service\OpenMeteoWeatherService;
use App\Service\WeatherServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'app:weather',
    description: 'Gets weather forecast',
)]
class WeatherCommand extends Command
{
    private LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }







    public function __construct(private readonly  WeatherServiceInterface $ws , string $name = null)
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

        $this->logger->debug("Service being used is " . get_class($this->ws));

        $response  = $this->ws->getWeather($lat, $lng);

        $this->displayForecast($response);


        return Command::SUCCESS;
    }

    /**
     * $forecast will contain the day as key then an array
     * containing min/max temperature as value
     * Example :
     * [
     *  '2023-08-26' => ['min' => 12.4, 'max' => 20.1]
     * ]
     */
    protected function displayForecast($forecast): void
    {
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table->setHeaders(['Day', 'Temperature Min', 'Temperature Max']);
        $rows = [];
        foreach ($forecast as $day => $temperature) {
            $rows[] = [
                $day,
                $temperature['min'],
                $temperature['max'],
            ];

        }
        $table->setRows($rows);
        $table->render();
    }

}
