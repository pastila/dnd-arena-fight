<?php

namespace Deployer;

use function Deployer\Support\array_to_string;

function dockerGetContainerId ($service)
{
  return run('cd {{release_path}} && docker-compose ps -q ' . $service);
}

function dockerServiceExists ($service)
{
  return !!dockerGetContainerId($service);
}

function dockerRemoveService ($service)
{
  $containerId = dockerGetContainerId($service);

  if (!$containerId)
  {
    throw new \InvalidArgumentException(`Container ${service} not found.`);
  }

  return run('docker rm -f ' . $containerId);
}

/**
 * Run a command using service, specified in docker-compose.yml file
 *
 * @param $service
 * @param $command
 * @param array $options
 */
function runInDocker ($service, $command, $options = [])
{
  $containerId = dockerGetContainerId($service);

  if (!$containerId)
  {
    throw new \InvalidArgumentException(`Container ${service} not found.`);
  }

  $command = parse($command);
  $workingPath = get('working_path', '');

  if (!empty($workingPath))
  {
    $command = "cd $workingPath && ($command)";
  }

  $env = get('env', []) + ($options['env'] ?? []);
  if (!empty($env))
  {
    $env = array_to_string($env);
    $command = "export $env; $command";
  }

  $opts = [];
  if (isset($options['user']))
  {
    $opts[] = '-u ' . $options['user'];
  }

  $command = sprintf('docker exec %s %s sh -c "%s"', implode(' ', $opts), $containerId, $command);

  return run($command);
}

set('docker_deploy_path', '/var/www');

/**
 * Install assets from public dir of bundles
 */
task('deploy:docker:assets:install', function()
{
  runInDocker(get('workspace_service'), '{{bin/php}} {{bin/console}} assets:install {{console_options}} {{docker_deploy_path}}/web', [
  ]);
})->desc('Install bundle assets');


/**
 * Dump all assets to the filesystem
 */
task('deploy:docker:assetic:dump', function()
{
  if (get('dump_assets'))
  {
    runInDocker(get('workspace_service'), '{{bin/php}} {{bin/console}} assetic:dump {{console_options}}', [
    ]);
  }
})->desc('Dump assets');

/**
 * Clear Cache
 */
task('deploy:docker:cache:clear', function()
{
  runInDocker(get('workspace_service'), '{{bin/php}} {{bin/console}} cache:clear {{console_options}} --no-warmup', [
  ]);
})->desc('Clear cache');

/**
 * Warm up cache
 */
task('deploy:docker:cache:warmup', function()
{
  runInDocker(get('workspace_service'), '{{bin/php}} {{bin/console}} cache:warmup {{console_options}}', [
  ]);
})->desc('Warm up cache');


/**
 * Migrate database
 */
task('deploy:docker:database:migrate', function()
{
  $options = '{{console_options}} --allow-no-migration';
  if (get('migrations_config') !== '')
  {
    $options = sprintf('%s --configuration={{release_path}}/{{migrations_config}}', $options);
  }

  runInDocker(get('workspace_service'), sprintf('{{bin/php}} {{bin/console}} doctrine:migrations:migrate %s', $options));
})->desc('Migrate database');

desc('Installing vendors');
task('deploy:docker:vendors', function()
{
  runInDocker(get('workspace_service'), '{{bin/composer}} {{composer_options}}', [
  ]);
});

desc('Fix file owner after build');
task('fix_owner', function()
{
  $previousReleaseExist = test('[ -h release ]');

  if ($previousReleaseExist)
  {
    run('chown `whoami`:`whoami` $(readlink release)');
  }
});

// build and start the workspace container
task('prepare_workspace', function()
{
  $workspaceService = get('workspace_service');

  if (dockerServiceExists($workspaceService))
  {
    dockerRemoveService($workspaceService);
  }

  run('cd {{release_path}} && docker-compose up -d ' . $workspaceService, ['env' => [
    'APP_CODE_PATH_HOST' => get('release_path')
  ]]);
  // fix ssh files owner
})->desc('Preparing workspace container');

task('start_services', function()
{
  $services = [];

  if (has('docker_start_services'))
  {
    $services = get('docker_start_services');
  }

  run('cd {{current_path}} && docker-compose up -d ' . implode(' ', $services));
});
