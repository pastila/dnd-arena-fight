<?php
/**
 *  (c) 2019 Общество с ограниченной ответственностью "Экьюрейт Веб". Все права защищены.
 *
 *  Настоящий файл является частью программного продукта, разработанного ООО "Экьюрейт Веб"
 *  (ОГРН 1186658025289, ИНН 6683013910).
 *
 *  Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 *  ООО "Экьюрейт Веб". Любое их использование без согласия ООО "Экьюрейт Веб" рассматривается,
 *  как нарушение его авторских прав.
 *   Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

namespace Deployer;

require 'recipe/symfony3.php';
require 'docker-deployer.php';

// Project name
set('application', 'abcg');

// Project repository
set('repository', 'git@git.accurateweb.ru:accurateweb/abcg.git');

// [Optional] Allocate tty for git clone. Default value is false.
//set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', [
  '.env',
//  'docker-compose.yml',
]);
add('shared_dirs', [
//  'var/synchronization',
  'var/uploads',
  'web/uploads',
  'web/filemanager',
]);

// Writable dirs by web server
add('writable_dirs', [
  'var/uploads',
  'web/uploads',
  'web/filemanager',
]);
set('allow_anonymous_stats', false);
set('ssh_multiplexing', false);
set('http_user', 'www-data');
set('workspace_service', 'php-fpm');

// Hosts
host('staging')
  ->hostname('staging.aw-dev.ru')
  ->stage('staging')
  ->user('deployer')
  ->set('deploy_path', '/var/www/sites/abcg')
  ->set('bin/php', 'php')
  ->set('branch', 'development')
//  ->set('branch', 'ABCG-181')
  ->set('bin/composer', 'composer')
  ->set('bin/console', '{{docker_deploy_path}}/bin/console')
  ->set('keep_releases', 2)
;

host('prod')
  ->hostname('88.99.184.234')
  ->stage('prod')
  ->user('deployer')
  ->set('deploy_path', '/var/www/abcg')
  ->set('bin/php', '/usr/bin/php')
  ->set('branch', 'master')
  ->set('keep_releases', 3)
;

host('prod-aw')
  ->hostname('staging.aw-dev.ru')
  ->stage('prod-aw')
  ->user('deployer')
  ->set('deploy_path', '/var/www/sites/abcg.pro')
  ->set('bin/php', 'php')
  ->set('branch', 'development')
//  ->set('branch', 'ABCG-181')
  ->set('bin/composer', 'composer')
  ->set('bin/console', '{{docker_deploy_path}}/bin/console')
  ->set('keep_releases', 2)
;

task('npm-install', function(){
  run('cd {{release_path}} && npm install --prefer-offline --no-audit');
})->desc('npm install');

task('npm-build', function(){
  run('cd {{release_path}} && grunt');
})->desc('Build assets');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('prepare_workspace')->onStage('staging', 'prod-aw');
task('deploy:docker:vendors')->onStage('staging', 'prod-aw');
task('deploy:docker:database:migrate')->onStage('staging', 'prod-aw');
task('deploy:docker:assets:install')->onStage('staging', 'prod-aw');
task('deploy:docker:cache:clear')->onStage('staging', 'prod-aw');
task('deploy:docker:cache:warmup')->onStage('staging', 'prod-aw');
task('start_services')->onStage('staging', 'prod-aw');

task('deploy:vendors')->onStage('prod');
task('npm-install')->onStage('prod');
task('npm-build')->onStage('prod');
task('database:migrate')->onStage('prod');
task('deploy:assets:install')->onStage('prod');
task('deploy:cache:clear')->onStage('prod');
task('deploy:cache:warmup')->onStage('prod');

task('docker:build-assets', function(){
  run('docker run --rm -v {{deploy_path}}/current:/var/www registry-gitlab.accurateweb.ru/accurateweb/abcg/node npm install --prefer-offline --no-audit');
  run('docker run --rm -v {{deploy_path}}/current:/var/www registry-gitlab.accurateweb.ru/accurateweb/abcg/node grunt');
})->onStage('staging');

task('elastica:populate', function(){
  run('{{bin/php}} {{bin/console}} fos:elastica:populate {{console_options}}');
})->onStage('prod');

task('docker:elastica:populate', function(){
  runInDocker(get('workspace_service'), '{{bin/php}} {{bin/console}} fos:elastica:populate {{console_options}}', [
  ]);
})->onStage('staging');

task('deploy', [
  'deploy:info',
  'deploy:prepare',
  'deploy:lock',
  'deploy:release',
  'deploy:update_code',
  'deploy:clear_paths',
  'deploy:create_cache_dir',
  'deploy:shared',
  'deploy:assets',
  # Bare metal deploys
  'deploy:vendors',
  'npm-install',
  'npm-build',
  'database:migrate',
  'deploy:assets:install',
  'deploy:cache:clear',
  'deploy:cache:warmup',
  # Docker deploys
  'prepare_workspace',
  'deploy:docker:vendors',
  'deploy:docker:database:migrate',
  'deploy:docker:assets:install',
  'deploy:docker:cache:clear',
  'deploy:docker:cache:warmup',
  'deploy:writable',

  'elastica:populate',
  'deploy:symlink',

  'start_services',
  'docker:elastica:populate',

  'deploy:unlock',
  'cleanup',
])->desc('Deploy your project');
