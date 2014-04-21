<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class SluggableTableCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sluggable:table';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a migration for the Sluggable database columns';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$fullPath = $this->createBaseMigration();

		file_put_contents($fullPath, $this->getMigrationStub());

		$this->info('Migration created successfully!');

		$this->call('dump-autoload');
	}

	/**
	 * Create a base migration file for the model.
	 *
	 * @return string
	 */
	protected function createBaseMigration()
	{
		$name = 'add_sluggable_columns';

		$path = $this->laravel['path'].'/database/migrations';

		return $this->laravel['migration.creator']->create($name, $path);
	}

	/**
	 * Get the contents of the sluggable migration stub.
	 *
	 * @return string
	 */
	protected function getMigrationStub()
	{
		$stub = file_get_contents(__DIR__.'/stubs/migration.stub');

		return str_replace(
			array('sluggable_table', 'sluggable_column'),
			array($this->argument('table'), $this->argument('column')),
			$stub
		);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('table',  InputArgument::REQUIRED, 'The name of your sluggable table.'),
			array('column', InputArgument::OPTIONAL, 'The name of your slugged column (defaults to "slug").', 'slug'),
		);
	}

}