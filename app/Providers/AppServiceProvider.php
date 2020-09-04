<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Components\Helpers\MenuBuilderHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        FormFacade::component('textInput', 'components.form.text_input', ['name', 'value', 'attributes']);
        FormFacade::component('hiddenInput', 'components.form.hidden_input', ['name', 'value', 'attributes']);
        FormFacade::component('fileInput', 'components.form.file_input', ['name', 'value', 'attributes']);
        FormFacade::component('emailInput', 'components.form.email_input', ['name', 'value', 'attributes']);
        FormFacade::component('passwordInput', 'components.form.password_input', ['name', 'value', 'attributes']);
        FormFacade::component('numberInput', 'components.form.number_input', ['name', 'value', 'attributes']);
        FormFacade::component('textareaInput', 'components.form.textarea_input', ['name', 'value', 'attributes']);
        FormFacade::component('dateInput', 'components.form.date_input', ['name', 'value', 'attributes']);
        FormFacade::component('daterangeInput', 'components.form.date_range_input', ['name', 'value', 'attributes']);
        FormFacade::component('checkboxInput', 'components.form.checkbox_input', ['name', 'value', 'options' => [1 => 'yes', 0 => 'no'], 'attributes']);
        FormFacade::component('radioInput', 'components.form.radio_input', ['name', 'value', 'options' => [1 => 'yes', 0 => 'no'], 'attributes']);
        FormFacade::component('switchInput', 'components.form.switch_input', ['name','value','attributes' => []]);
        FormFacade::component('multipleswitchInput', 'components.form.multipleswitch_input', ['name','value','columns','attributes']);
        FormFacade::component('multiplecolumnInput', 'components.form.multiplecolumn_input', ['name','value','columns','includeId']);
        
        FormFacade::component('locationInput', 'components.form.location_input', ['name', 'value', 'attributes']);

        FormFacade::component('multipleInput', 'components.form.multiple_input', ['name', 'type', 'values' => [''], 'attributes']);
        FormFacade::component('multipleColumnInput', 'components.form.multiple_column_input', ['name', 'values' => [''], 'columns', 'attributes']);
        
        FormFacade::component('selectInput', 'components.form.select_input', ['name', 'value', 'options' => [], 'attributes']);
        FormFacade::component('select2Input', 'components.form.select2_input', ['name', 'value', 'options' => [], 'attributes']);
        FormFacade::component('select2CheckboxInput', 'components.form.select2_checkbox_input', ['name', 'id', 'route', 'attributes' => []]);
        FormFacade::component('selectTreeInput', 'components.form.select_tree_input', ['name', 'input', 'route', 'attributes' => []]);        
        FormFacade::component('select2MultipleInput', 'components.form.select2_multiple_input', ['name', 'value' => [], 'options' => [], 'attributes']);

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $menu = MenuBuilderHelper::getMenu();
            foreach ($menu as $key => $value) {
                $var = $value;
                if (array_key_exists($value['key'], getConfigMenu('icon'))) {
                    $var['icon'] = getConfigMenu('icon')[$value['key']];
                }
                if (array_key_exists($value['key'], getConfigMenu('label'))) {
                    $var['label'] = getConfigMenu('label')[$value['key']];
                }
                if (array_key_exists($value['key'], getConfigMenu('text'))) {
                    $var['text'] = getConfigMenu('text')[$value['key']];
                }
                if (array_key_exists($value['key'], getConfigMenu('separator'))) {
                    $event->menu->add(getConfigMenu('separator')[$value['key']]);
                }
                $event->menu->add($var);
            }
        });
    }
}
