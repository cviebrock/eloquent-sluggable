<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CreateSlugsCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slugs:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create slugs for Model.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $model_name = ucfirst($this->argument('model'));

        $model = new $model_name;

        try
        {
            $model = new $model_name;
        }
        catch(Exception $e)
        {
            $this->error('The model was not found');
        }
        $result = $model::all();

        foreach($result as $item) {

            if($this->option('force'))
            {
                $item->resluggify();
            }
            else
            {
                $item->sluggify();
            }

            $item->save();
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('model', InputArgument::REQUIRED, 'The model you want to update the slugs for.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('force', null, InputOption::VALUE_NONE, 'Overwrite existing slugs.', null),
        );
    }

}
