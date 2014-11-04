<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SluggableSluggifyCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sluggable:sluggify';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Runs the resluggify on all records for a given model';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
	  $model = $this->argument('model');

    $this->output->write("Sluggifying $model... ");
	  foreach ($model::all() as $record)
	  {
	    $record->sluggify($this->option('overwrite'));
	    $record->save();
    }

		$this->info(count($model::all()) . ' records slugged!');
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('model',  InputArgument::REQUIRED, 'The fully qualified class name of your model. ie: "App\\Models\\CardModel".'),	
		);
	}
	
	protected function getOptions()
	{
	  return array(
			array('overwrite',  null, InputOption::VALUE_NONE, 'Whether to overwrite the existing slug.'),			
    );
  }
  

}