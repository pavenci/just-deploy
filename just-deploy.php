<?php
/**
 * just-deploy
 * Приложение для развёртывания проектов из удаленного git репозитория
 * @author Pavel Andreev
 * @version 1.1
 * @license MIT
 */

require_once 'just-deploy.config.php';

// Файлы и каталоги проекта, которые не должны участвовать в синхронизации и развёртывании
$rsyncExclude = 'just-deploy.exclude.txt';
// Каталог для хранения резервной копии проекта
$backupDirectory = '../just-deploy/backup';
// rsync команда
$rsyncCommand = 'rsync -avh --progress --delete';


if ($argv[1] === 'deploy') {
    // Каталог с исходным репозиторием и веткой master
    $originDirectory = '../just-deploy/git/origin';
    // Каталог с целевой веткой, если она отличается от master
    $targetDirectory = '../just-deploy/git/target';


    // Клонирование репозитория в нужный каталог на сервере
    shell_exec('mkdir ../just-deploy ../just-deploy/git ' . $originDirectory);
    shell_exec('git clone ' . $remoteRepositoryLink . ' ' . $originDirectory);

    // Создание каталога для ветки, отличной от master
    if ($targetBranch !== 'master') {
        shell_exec('cd ' . $originDirectory . ' && git worktree add ../target ' . $targetBranch . '&& cd ' . __DIR__);
    }


    // Создаём резервную копию рабочего каталога перед развёртыванием
    if ($projectBackup === 'YES') {
        echo 'Началось создание резервной копии проекта...' . PHP_EOL;
        shell_exec('mkdir ' . $backupDirectory);
        shell_exec('rsync -avh --progress ./ ' . $backupDirectory . '/');
        echo 'Создание резервной копии проекта завершено.' . PHP_EOL;
    } else if ($projectBackup === 'NO') {
        echo 'Резервная копия отключена. В случае необходимости, вы не сможете восстановить проект.' . PHP_EOL;
    } else {
        echo 'Ошибка! Проверьте корректность исходных данных.'. PHP_EOL;
    }


    // Развёртывание проекта из репозитория в рабочий каталог с помощью rsync
    if ($targetBranch === 'master') {
        echo 'Идёт развёртывание...' . PHP_EOL;
        shell_exec($rsyncCommand . ' --exclude-from=' . $rsyncExclude . ' ' . $originDirectory . '/ ./');
        echo 'Развёртывание из ветки ' . $targetBranch . ' завершено.' . PHP_EOL;
        shell_exec('rm -rf ' . $originDirectory);
    } else if ($targetBranch !== 'master') {
        echo 'Идёт развёртывание...' . PHP_EOL;
        shell_exec($rsyncCommand . ' --exclude-from=' . $rsyncExclude . ' ' . $targetDirectory . '/ ./');
        echo 'Развёртывание из ветки ' . $targetBranch . ' завершено.' . PHP_EOL;
        shell_exec('rm -rf ' . $originDirectory . ' ' . $targetDirectory);
    } else {
        echo 'Ошибка! Проверьте корректность исходных данных.' . PHP_EOL;
    }


    // Выполнение пользовательских команд после развёртывания проекта
    echo 'Выполнение пользовательских команд...' . PHP_EOL;
    shell_exec($commands);

} else if ($argv[1] === 'backup') {

    if ($projectBackup === 'YES') {
        echo 'Идёт восстановление проекта...' . PHP_EOL;
        shell_exec($rsyncCommand . ' ' . $backupDirectory . '/ ./');
        echo 'Восстановление проекта из резервной копии завершено.' . PHP_EOL;
    } else if ($projectBackup === 'NO') {
        echo 'Невозможно восстановить проект. Резервная копия отключена в just-deploy.config.php' . PHP_EOL;
    } else {
        echo 'Ошибка! Проверьте корректность исходных данных.' . PHP_EOL;
    }

} else {
    echo 'Ошибка! Проверьте корректность исходных данных.' . PHP_EOL;
}