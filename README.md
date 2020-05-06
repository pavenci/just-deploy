# just-deploy
Простое развёртывание проектов из удаленного git репозитория.

## Руководство к использованию

Файлы ```just-deploy.php, just-deploy.config.php, just-deploy.exclude.txt``` необходимо поместить в каталог с кодом проекта, куда планируется осуществлять развёртывание.

В файле ```just-deploy.config.php``` в значение для ```$remoteRepositoryLink``` поставьте ссылку на ваш удалённый репозиторий, с которого будет происходить развёртывание. Например, ```git@github.com:pavenci/just-deploy.git```.  
В значение для ```$targetBranch``` введите ветку, с которой будет развёртываться код. По умолчанию это ```master```. 

Также приложение поддерживает функцию резервного копирования проекта и отката в случае неудачного развёртывания. Для этого поставьте ```YES``` в значение для ```$projectBackup```. 

Файл ```just-deploy.exclude.txt``` предназначен для указания исключений в виде папок или файлов, которые будут проигнорированы при синхронизации. Приложение синхронизируется с кодом из нужной ветки репозитория и удаляет все каталоги и файлы, которых нет в свежей версии репозитория. Все каталоги и файлы, которые не должны быть в репозитории, но должны быть в каталоге проекта, необходимо прописать в этом файле. Это чем-то напоминает .gitignore.

### Дополнительные условия

Необходимо подключить публичный ssh ключ вашей серверной учетной записи в настройках git-хостинга, например github. 
А также необходимо убедиться, что у вашей учетной записи есть права на запись в каталоге вашего проекта.

## Лицензия
Для этого проекта действует [лицензия MIT](https://opensource.org/licenses/MIT). 

## Требования к системе

Для корректной работы приложения требуется наличие rsync и git на сервере. Приложение совместимо с shared хостингами.

