<?php

namespace Windsor\CLI;

use Minicli\App;
use Minicli\Command\CommandCall;
use Windsor\CLI\Commands\Init;
use Windsor\CLI\Commands\Install;
use Windsor\CLI\Commands\Model;
use Windsor\CLI\Commands\Trigger;
use Windsor\CLI\Commands\Work;

class CommandRegistry
{

    private string $projectDir;

    protected function map(): array {
        return [
            'work' => Work::class,
            'init' => Init::class,
            'model' => Model::class,
            'trigger' => Trigger::class,
            'install' => Install::class
        ];
    }

    public function __construct(App $app, CommandCall $call, string $projectDir)
    {
        $this->projectDir = $projectDir;
        foreach($this->map() as $command => $class) {
            $this->register($command, $class, $app, $call);
        }
    }

    public static function createApp($argv, $projectDir)
    {
        $app = new App();
        $input = new CommandCall($argv);
        new self($app, $input, $projectDir);
        $app->runCommand($input->getRawArgs());
    }

    private function register(string $command, string $class, App $app, CommandCall $call) {
        $dir = $this->projectDir;
        $app->registerCommand(strtolower($command), function () use ($class, $app, $call, $dir) {
            $command = new $class($app, $call, $dir);
        });
    }

}