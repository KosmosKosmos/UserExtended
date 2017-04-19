<?php namespace Clake\Userextended\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Clake\UserExtended\Classes\ModuleManager;
use Clake\UserExtended\Classes\UserExtended;
use Clake\Userextended\Models\Settings;
use Redirect;
use Session;
use Schema;
use Db;
use Backend;

/**
 * User Extended by Shawn Clake
 * Class Modules
 * User Extended is licensed under the MIT license.
 *
 * @author Shawn Clake <shawn.clake@gmail.com>
 * @link https://github.com/ShawnClake/UserExtended
 *
 * @license https://github.com/ShawnClake/UserExtended/blob/master/LICENSE MIT
 * @package Clake\Userextended\Controllers
 */
class Modules extends Controller
{

    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    //public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        // Setting this context so that our sidebar menu works
        //BackendMenu::setContext('October.System', 'system', 'settings');
        //SettingsManager::setContext('clake.userextended', 'settings');

        BackendMenu::setContext('RainLab.User', 'user', 'modules');

        //Add CSS for some backend menus
        $this->addCss('/plugins/clake/userextended/assets/css/backend.css');
        $this->addJs('/plugins/clake/userextended/assets/js/backend.js');
    }

    public function manage()
    {
        $this->pageTitle = "Manage Modules";
        $modules = ModuleManager::all()->getModules();
        foreach($modules as $module)
        {
            $injectionStr = '';
            $flags = $module->flags;
            foreach($flags as $flag=>$enabled)
            {
                if($enabled)
                {
                    $name = substr($flag, 6);
                    $injectionStr .= $name . ', ';
                }
            }
            $module->injectionStr = $injectionStr;
        }

        $this->vars['modules'] = $modules;
        $this->vars['devMode'] = Settings::get('dev_mode', false);
    }


    public function onRefreshModules()
    {
        ModuleManager::all()->refresh();
        return Redirect::to(Backend::url('clake/userextended/modules/manage'));
    }

    public function onViewDocumentation()
    {
        $name = post('name');
        $documentation = UserExtended::$name()->getDocumentation();
        return $this->makePartial('view_documentation', ['content' => $documentation]);
    }

    public function onViewUpdateNotes()
    {
        $name = post('name');
        $updateNotes = UserExtended::$name()->getUpdateNotes();
        return $this->makePartial('view_update_notes', ['content' => $updateNotes]);
    }

    public function onEditModule()
    {
        $name = post('name');
        $module = ModuleManager::findModule($name);
        return $this->makePartial('update_module_form', ['module' => $module]);
    }



}