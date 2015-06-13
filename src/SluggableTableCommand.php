<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputArgument;


class SluggableTableCommand extends BaseCommand {

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
	 * @var SluggableMigrationCreator
	 */
	protected $creator;

	/**
	 * @var \Illuminate\Foundation\Composer
	 */
	protected $composer;

	/**
	 * Create a new migration sluggable instance.
	 *
	 * @param SluggableMigrationCreator $creator
	 * @param Composer $composer
	 */
	public function __construct(SluggableMigrationCreator $creator, Composer $composer) {
		parent::__construct();

		$this->creator = $creator;
		$this->composer = $composer;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire() {
		$table = $this->input->getArgument('table');

		$column = $this->input->getArgument('column');

		$name = 'add_' . $table . '_' . $column . '_column';

		// Now we are ready to write the migration out to disk. Once we've written
		// the migration out, we will dump-autoload for the entire framework to
		// make sure that the migrations are registered by the class loaders.
		$this->writeMigration($name, $table, $column);

		$this->composer->dumpAutoloads();
	}

	/**
	 * Write the migration file to disk.
	 *
	 * @param  string $name
	 * @param  string $table
	 * @param  bool $column
	 * @return string
	 */
	protected function writeMigration($name, $table, $column) {
		$path = $this->getMigrationPath();

		$this->creator->setColumn($column);

		$file = pathinfo($this->creator->create($name, $path, $table), PATHINFO_FILENAME);

		$this->line("<info>Created Migration:</info> $file");
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments() {
		return [
			['table', InputArgument::REQUIRED, 'The name of your sluggable table.'],
			['column', InputArgument::OPTIONAL, 'The name of your slugged column (defaults to "slug").', 'slug'],
		];
	}
}
