<?php
declare(strict_types=1);
return ['roles' => ['admin' => ['*'], 'editor' => ['posts.create','posts.edit','posts.delete'], 'viewer' => ['posts.view']]];
