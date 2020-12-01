<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:builder 
        {model : Model name with directory path that separated with slash, example: Models/User} 
        {--f|field= : Fields and data type you want to include on the Model (use rules formatting in double quotes, field with %_id will defined as foreign), field rule start with @ and separated by |, field rule with * at the beginning (ex:*datetime) will become database data type.  example: "field_name@string|required field2_name@*text|string|required|min:8|max:10"}
        {--e|except-foreign= : Use to except foreign field, example: tawkto_id, use comma to separate multiple foreign}
        {--r|route= : Use to disable route generator, example: false}
        {--o|x-model= : Use to disable model, example: false}
        {--m|migration= : Use to disable migration generator, example: false}
        {--x|x-migrate= : Use to disable auto migrate command, example: false}
        {--c|core-only= : Use to generate migration and model only, example: true}';

    protected $namespace = '\\App\\', $modelWithNamespace;
    protected $fields = [], $rules = [], $fExcept = [];


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration, model, controller, form and datatable';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelTmp = explode("\\", $this->argument('model'));
        $modelTmp = count($modelTmp) == 1 ? explode("/", $this->argument('model')) : $modelTmp;
        $model    = end($modelTmp);

        array_pop($modelTmp);

        $modelSnake     = Str::snake($model);
        $modelDir       = implode("\\", $modelTmp);

        $this->modelWithNamespace = $this->namespace.(!empty($modelDir) ? $modelDir."\\" : "").$model;

        $filterDir      = 'Components\\Filters';
        $jobDir         = 'Jobs';

        $migrationInput = @$this->option('migration') ?? 'true';
        $issetEquals    = explode("=", $migrationInput);
        $migrationInput = count($issetEquals) > 1 ? $issetEquals[1] : $migrationInput;
        
        $routeInput  = @$this->option('route') ?? 'true';
        $issetEquals = explode("=", $routeInput);
        $routeInput  = count($issetEquals) > 1 ? $issetEquals[1] : $routeInput;
        
        $xModel      = @$this->option('x-model') ?? 'true';
        $issetEquals = explode("=", $xModel);
        $xModel      = count($issetEquals) > 1 ? $issetEquals[1] : $xModel;
        
        $xMigrate    = @$this->option('x-migrate') ?? 'true';
        $issetEquals = explode("=", $xMigrate);
        $xMigrate    = count($issetEquals) > 1 ? $issetEquals[1] : $xMigrate;

        $MMO         = @$this->option('core-only') ?? 'false';
        $issetEquals = explode("=", $MMO);
        $MMO         = count($issetEquals) > 1 ? $issetEquals[1] : $MMO;

        $fExcept     = @$this->option('except-foreign') ?? '';
        $issetEquals = explode("=", $fExcept);
        $fExcept     = count($issetEquals) > 1 ? $issetEquals[1] : $fExcept;
        $fExcept     = !empty($fExcept) ? explode(",", $fExcept) : [];
        $fExcept     = (count($fExcept) > 0) ? array_unshift($fExcept,"~!@#$%^&*(*&^%$%^&*()") : [];
        
        $fieldInput  = @$this->option('field') ?? '';
        $issetEquals = explode("=", $fieldInput);
        $fieldInput  = count($issetEquals) > 1 ? $issetEquals[1] : $fieldInput;
        $fieldInput  = explode(" ", $fieldInput);

        $fields      = [];
        $rules       = [];
        $dType       = [];

        foreach ($fieldInput as $key => $value) {
            $tmp         = explode("@", $value);
            $issetEquals = explode("=", $tmp[0]);
            $fields[]    = $key == 0 && count($issetEquals) > 1 ? $issetEquals[1] : $tmp[0];
            
            $tmpRule     = "";
            $tmpType     = "";
            $tmpField    = count($tmp) > 1 ? explode("|", $tmp[1]) : [];
            if (count($tmpField) > 0) {
                $tmpRule = [];
                foreach ($tmpField as $bKey => $bValue) {
                    $tmpBV = explode("*", $bValue);
                    if (count($tmpBV) > 1) {
                        $tmpType = $tmpBV[1];
                    } else {
                        $tmpRule[] = $bValue;
                    }
                }
                $tmpRule = implode("|", $tmpRule);
            }

            $rules[]     = $tmpRule;
            $dType[]     = $tmpType;
        }

        $this->fields  = $fields;
        $this->rules   = $rules;
        $this->fExcept = $fExcept;

        if ($migrationInput == 'true') {
            echo "\nGenerating Migration...";
            $this->generateMigration($model, stringToTable($modelSnake), $dType, $xMigrate);
            echo "success";
        }

        if ($xModel == 'true') {
            echo "\nGenerating Model...";
            $this->generateModel($model, $modelDir);
            echo "success";
        }

        if ($MMO == 'false') {
            echo "\nGenerating Controller...";
            $this->generateController($model);        
            echo "success";
            echo "\nGenerating Filter...";
            $this->generateFilter($model, $filterDir);
            echo "success";

            echo "\nGenerating Job Group...";
            echo "\n\tImport Template";
            $this->generateImportTemplate($model);
            echo "\n\tImport Template OK.";
            echo "\n\tImport Function";
            $this->generateImportClass($model);
            echo "\n\tImport Function OK.";
            echo "\n\tExport Function";
            $this->generateExportClass($model);
            echo "\n\tExport Function OK.";
            echo "\nsuccess";

            echo "\nGenerating Route...";
            $this->generateRoutes($model);
            echo "success";
        }

        echo "\nGenerate Builder success.";
    }

    protected function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function generateMigration($name, $table, $dType, $migrate = '')
    {
        $tableColumn = [];
        foreach ($this->fields as $key => $value) {
            $rule = $this->rules[$key];
            $type = $dType[$key];
            $tableColumn[] = ruleToMigrationColumn($value, $type, $rule, $this->fExcept);
        }

        $tableColumn     = implode("\t\t\t\t", $tableColumn);
        $createMigration = now()->format('Y_m_d_His')."_create_".$table."_table";

        $template = str_replace(
            [
                '{{modelName}}',
                '{{tableName}}',
                '{{tableColumn}}'
            ],
            [
                stringToTable($name),
                $table,
                $tableColumn
            ],
            $this->getStub('Migration')
        );

        file_put_contents(database_path("migrations/{$createMigration}.php"), $template);
        empty($migrate) ? $this->call("migrate") : '';
    }    

    protected function generateController($name)
    {
        $route = Str::kebab($name);
        $title = ucwords(str_replace('-', ' ', $route));
        $table = stringToTable( Str::snake($name) );
        $namespace    = $this->modelWithNamespace;
        $defEloquent  = generateDefaultEloquent($this->modelWithNamespace, $name, $this->fields, $this->rules, $this->fExcept);
        $customHelper = generateCustomHelper($this->fExcept);
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelTitle}}',
                '{{modelRoute}}',
                '{{modelTable}}',
                '{{modelNameSpace}}',
                '{{modelNameSingularLowerCase}}',
                '{{defaultJoin}}',
                '{{defaultSelect}}',
                '{{customHelper}}'
            ],
            [
                $name,
                $title,
                $route,
                $table,
                $namespace,
                lcfirst($name),
                $defEloquent['join'],
                $defEloquent['select'],
                $customHelper
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
    }

    protected function generateModel($name, $modelDir)
    {
        $dir = (!empty($modelDir) ? "\\".$modelDir : "");
        $modelRules = generateTextRule($this->fields, $this->rules, $this->modelWithNamespace, $this->fExcept);

        $rules    = $modelRules['rules'];
        $function = $modelRules['function'];

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSpace}}',
                '{{modelRules}}',
                '{{modelFunction}}',
            ],
            [
                $name,
                $dir,
                $rules,
                $function
            ],
            $this->getStub('Model')
        );

        !is_dir(app_path($modelDir)) ? mkdir(app_path($modelDir)) : '';
        file_put_contents(app_path($modelDir."/{$name}.php"), $modelTemplate);
    }

    protected function generateFilter($name, $filterDir)
    {
        $dir = (!empty($filterDir) ? "\\".$filterDir : "");
        $modelFilters = generateTextFilter($this->fields, $this->rules, $name, $this->fExcept);

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSpace}}',
                '{{modelFilters}}',
                '{{additionalNamespace}}',
            ],
            [
                $name,
                (!empty($filterDir) ? "\\".$filterDir : ""),
                $modelFilters[0],
                $modelFilters[1]
            ],
            $this->getStub('Filter')
        );
        if(!is_dir(app_path($filterDir))) mkdir(app_path($filterDir));
        file_put_contents(app_path($filterDir."/{$name}Filter.php"), $modelTemplate);
    }

    protected function generateImportTemplate($name)
    {
        $model    = $this->modelWithNamespace;
        $foreigns = getForeigns($this->fields, $model, $this->fExcept);

        $arrayForeign = [];

        if(!is_dir(app_path("Templates"))) mkdir(app_path("Templates"));

        foreach ($foreigns as $key => $foreign) {
            $modelForeign = explode("\\", $foreign['model']);
            $modelForeign = end($modelForeign);
            $route    = Str::kebab($modelForeign);
            $title    = ucwords(str_replace('-', ' ', $route));
            $modelDataTemplate = str_replace(
                [
                    '{{modelName}}',
                    '{{foreignTitle}}',
                    '{{foreign}}',
                    '{{foreignColumn}}',
                    '{{foreignModelNamespace}}',
                    '{{foreignSelect}}',
                ],
                [
                    $name,
                    $title,
                    $modelForeign,
                    $foreign['column'],
                    $foreign['model'],
                    $foreign['select'],
                ],
                $this->getStub('ImportDataTemplate')
            );
            file_put_contents(app_path("Templates/{$name}ImportData{$modelForeign}Template.php"), $modelDataTemplate);

            $arrayForeign[] = '\''.$name.'ImportData'.$modelForeign.'Template'.'\'';
        }
        $modelInputTemplate = str_replace(
            [
                '{{modelName}}',
                '{{headerColumn}}'
            ],
            [
                $name,
                toColumnHeader($this->fields, $this->fExcept, null, true, $model),
            ],
            $this->getStub('ImportInputTemplate')
        );

        $modelSheetTemplate = str_replace(
            [
                '{{modelName}}',
                '{{headerColumn}}',
                '{{arrayForeign}}'
            ],
            [
                $name,
                toColumnHeader($this->fields, $this->fExcept, null, true, $model),
                implode(',', $arrayForeign)
            ],
            $this->getStub('ImportSheetTemplate')
        );

        file_put_contents(app_path("Templates/{$name}ImportInputTemplate.php"), $modelInputTemplate);
        file_put_contents(app_path("Templates/{$name}ImportSheetTemplate.php"), $modelSheetTemplate);
    }

    protected function generateImportClass($name)
    {
        $model = $this->modelWithNamespace;
        if(!is_dir(app_path("Imports"))) mkdir(app_path("Imports"));

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
            ],
            [
                $name,
            ],
            $this->getStub('Import')
        );
        file_put_contents(app_path("Imports/{$name}Import.php"), $modelTemplate);

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSpace}}',
                '{{arrayColumn}}',
                '{{relationFinder}}'
            ],
            [
                $name,
                $model,
                toAssignedString($this->fields, $this->fExcept, true),
                toRelationFinder($this->fields, $model, $this->fExcept)
            ],
            $this->getStub('ImportSheet')
        );
        file_put_contents(app_path("Imports/{$name}ImportSheet.php"), $modelTemplate);
    }

    protected function generateExportClass($name)
    {
        $model       = $this->modelWithNamespace;
        $defEloquent = generateDefaultEloquent($model, $name, $this->fields, $this->rules, $this->fExcept, true);

    if(!is_dir(app_path("Exports"))) mkdir(app_path("Exports"));

        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSpace}}',
                '{{arrayColumn}}',
                '{{headerColumn}}',
                '{{defaultJoin}}',
                '{{defaultSelect}}'
            ],
            [
                $name,
                $model,
                toAssignedString($this->fields),
                toColumnHeader($this->fields, $this->fExcept),
                $defEloquent['join'],
                $defEloquent['select'],
            ],
            $this->getStub('ExportXls')
        );
        file_put_contents(app_path("Exports/{$name}ExportXls.php"), $modelTemplate);

        $cPdf        = toColumnPdf($this->fields, $this->rules, $model, $this->fExcept);
        $orientation = count($this->fields) > 4 ? "landscape" : "potrait";
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSpace}}',
                '{{headerColumn}}',
                '{{columnName}}',
                '{{defaultJoin}}',
                '{{defaultSelect}}',
                '{{orientation}}'
            ],
            [
                $name,
                $model,
                $cPdf['header'],
                $cPdf['columns'],
                $defEloquent['join'],
                $defEloquent['select'],
                $orientation
            ],
            $this->getStub('ExportPdf')
        );
        file_put_contents(app_path("Exports/{$name}ExportPdf.php"), $modelTemplate);
    }

    protected function generateRoutes($model)
    {
        $modelWithNamespace = $this->modelWithNamespace;
        // Generate API Routes
        $my_file = base_path('routes\api.php');
        $handle  = fopen($my_file, 'a+') or die('Cannot open file:  '.$my_file);
        $header  = "\n\nRoute::group(['prefix' => '".$modelWithNamespace::toKey()['route']."','middleware' => ['auth:api']], function() {\n";
        $footer  = "});";
        
        $route_list = '';
        foreach ($this->rest() as $key => $value) {
            $route_list .= "\tRoute::".$value['method']."('".$value['url']."', '".$model."Controller@".$value['function']."')->name('".$modelWithNamespace::toKey()['route'].".".$key."')";
            if($value['url'] == "{id}"){
                $route_list .= "->where('id', '[0-9]+');\n";
            }else{
                $route_list .= ";\n";
            }
        }
        
        fwrite($handle, $header);
        fwrite($handle, $route_list);
        fwrite($handle, $footer);

        // Generate Web Routes
        $my_file2 = base_path('routes\web.php');
        $handle2 = fopen($my_file2, 'a+') or die('Cannot open file:  '.$my_file2);
        $route_list2 = 
            "\nRoute::group(['prefix' => '".$modelWithNamespace::toKey()['route']."','middleware' => ['auth']], function() {\n".
                "\tRoute::get('', '".$model."Controller@index')->name('".$modelWithNamespace::toKey()['route'].".index');\n".
                "\tRoute::get('import-template', '".$model."Controller@importTemplate')->name('".$modelWithNamespace::toKey()['route'].".import-template');\n".
            "});\n";
        fwrite($handle2, $route_list2);
    }

    protected function rest()
    {
        return [
            'list' => [
                'method'   => 'get',
                'url'      => '',
                'function' => 'list'
            ],
            'create' => [
                'method'   => 'post',
                'url'      => '',
                'function' => 'store'
            ],
            'detail' => [
                'method'   => 'get',
                'url'      => '{id}',
                'function' => 'detail'
            ],
            'edit' => [
                'method'   => 'put',
                'url'      => '{id}',
                'function' => 'update'
            ],
            'delete' => [
                'method'   => 'delete',
                'url'      => '{id}',
                'function' => 'destroy'
            ],
            'datatable' => [
                'method'   => 'post',
                'url'      => 'datatable',
                'function' => 'datatable'
            ],
            'export-xls' => [
                'method'   => 'post',
                'url'      => 'export-xls',
                'function' => 'exportXls'
            ],
            'export-pdf' => [
                'method'   => 'post',
                'url'      => 'export-pdf',
                'function' => 'exportPdf'
            ],
            'import' => [
                'method'   => 'post',
                'url'      => 'import',
                'function' => 'import'
            ],
            'select2' => [
                'method'   => 'post',
                'url'      => 'select2',
                'function' => 'select2'
            ],
        ];
    }
}
