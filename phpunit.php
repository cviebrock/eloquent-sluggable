<?php

/**
 * Register composer auto-loader
 */

require __DIR__.'/vendor/autoload.php';


/**
 * Initialize Capsule
 */

$capsule = new Illuminate\Database\Capsule\Manager;

$capsule->addConnection(require(__DIR__.'/tests/config/database.php'));

$capsule->setEventDispatcher(new Illuminate\Events\Dispatcher);

$capsule->bootEloquent();

$capsule->setAsGlobal();


/**
 * Manually load required models
 */

require __DIR__.'/tests/models/Post.php';
