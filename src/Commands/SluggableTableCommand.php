<?php namespace Cviebrock\EloquentSluggable;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class SluggableTableCommand
 *
 * @package Cviebrock\EloquentSluggable
 */
class SluggableTableCommand extends BaseCommand
{
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
     * Create a new migration sluggable instance.
     *
     * @param SluggableMigrationCreator $creator
     */
    public function __construct(SluggableMigrationCreator $creator) {
        parent::__construct();

        $this->creator = $creator;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $table = $this->input->getArgument('table');

        $column = $this->input->getArgument('column');

        $name = 'add_' . $column . '_to_' . $table . '_table';

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will remind the user to `composer dump-autoload`
        // to make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table, $column);

        $this->line('<info>Don\'t forget to run</info> composer dump-autoload <info>to register the migration.</info>');
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string $name
     * @param  string $table
     * @param  bool $column
     * @return string
     */
    protected function writeMigration($name, $table, $column)
    {
        $path = $this->getMigrationPath();

        $this->creator->setColumn($column);

        $file = pathinfo($this->creator->create($name, $path, $table),
          PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> $file");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
          [
            'table',
            InputArgument::REQUIRED,
            'The name of your sluggable table.'
          ],
          [
            'column',
            InputArgument::OPTIONAL,
            'The name of your slugged column (defaults to "slug").',
            'slug'
          ],
        ];
    }
}
