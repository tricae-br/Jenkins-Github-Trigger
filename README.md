Tricae
===

Github Pull Request Build Trigger
---

>>> This software intends to be an alternative to Jenkins' *PullRequest Build Trigger* Plugin
>>> The situation is: you have to connect with github in order to listen PullRequest Hooks
>>> and your Jenkins have no access from internet but still have connection to it.
>>> the success and failure will be commented on PullRequest in order to allow Code Reviews before merge

### Requirements

* Jenkins
* PHP 5.3+

### Installation

#### PHP
* run ```php composer.phar install``` and wait for dependency installation
* copy application/settings.template into application/settings.php and change it for your needs
 - *Attention* on **jenkins -> branchVariable** and **jenkins -> token** parameter and for Jenkins step

#### Jenkins


* create/edit the project to be triggered
 * check *This Build is parametrized*
 * set up a global variable string parameter with name set on PHP Step
 * On *Build Triggers* check *Trigger builds remotely (e.g., from scripts)*
 * Fill *token* field with the same on PHP Step
* Set up the success and failure calling ```php cli/run.php --success``` or ```php cli/run.php --failure```
 * You can use BuildResultTrigger Plug-in or building another projects to do this

#### Cron

Call ```php cli/run.php --trigger``` on your cronjob (you can use a Jenkins job for that)

### On build success:

Call ```php cli/run.php --success```

### On build failure:

Call ```php cli/run.php --failure```