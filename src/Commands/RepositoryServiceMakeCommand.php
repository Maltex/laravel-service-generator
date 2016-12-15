<?php

namespace Maltex\Generators\Commands;

use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RepositoryServiceMakeCommand extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The command signature
     *
     * @var string
     */
    protected $signature = 'make:repo-service 
                            {name : The name of your service} 
                            {--repo= : The name of the repository}
                            {--func= : The name of repository function';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service class for a repository';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the command
     */
    public function handle()
    {
        $this->makeService();

        $this->composer->dumpAutoloads();
    }

    /**
     * Create a service
     *
     * @return mixed
     */
    protected function makeService()
    {
        if($this->serviceFileExists()) {
            return $this->error("A service with name or filename already exists");
        }

        $this->createServiceDir();

        $this->addServiceFile();
    }

    /**
     * Check for existing file name
     *
     * @return mixed
     */
    public function serviceFileExists()
    {
        return $this->files->exists($this->getPath());
    }

    /**
     * Create the service directory
     */
    protected function createServiceDir()
    {
        if (!$this->files->isDirectory(dirname($this->getPath()))) {
            $this->files->makeDirectory(dirname($this->getPath()), 0777, true, true);
        }
    }

    /**
     * Move generated service file to services directory
     *
     * @return void
     */
    protected function addServiceFile()
    {
        $this->files->put($this->getPath(), $this->compileServiceStub());

        $this->info('Service created successfully.');
    }

    /**
     * Replace stub placeholders and configure file
     *
     * @return string
     */
    protected function compileServiceStub()
    {
        $stub = $this->files->get($this->getStub());

        $this->replaceRepoDependency($stub)
            ->replaceClassName($stub)
            ->replaceRepoServiceFunction($stub);

        return $stub;
    }

    /**
     * Configure a function name for the repository
     * based on the service.
     *
     * @param $stub
     * @return $this
     */
    protected function replaceRepoDependency(&$stub)
    {
        // not required for non-repo
        if(is_null($this->getRepository())) {
            return $this;
        }

        $repositoryNamespace = $this->getAppNamespace() . $this->configureRepositoryName();

        $stub = str_replace('{{repositoryClass}}', $repositoryNamespace, $stub);

        return $this;
    }

    /**
     * Replace the class name in the stub.
     *
     * @param  string $stub
     * @return $this
     */
    protected function replaceClassName(&$stub)
    {
        $className = ucwords(camel_case($this->getServiceName()));
        
        $stub = str_replace('{{class}}', $className, $stub);
        
        return $this;
    }

    /**
     * Replace the function name for service
     *
     * @param $stub
     * @return $this
     */
    protected function replaceRepoServiceFunction(&$stub)
    {
        // not required for non-repo
        if(is_null($this->getRepository())) {
            return $this;
        }

        $stub = str_replace('{{serviceFunction}}', $this->configureRepositoryFunctionName(), $stub);

        return $this;
    }

    /**
     * Get the stub file to configure
     *
     * @return string
     */
    protected function getStub()
    {
        // switch stub depending on repo option
        $fileName = is_null($this->getRepository()) ? 'service.stub' : 'service-with-repo.stub';
        
        return __DIR__ . '/../stubs/' .$fileName;
    }

    /**
     * Get the service directory
     *
     * @return string
     */
    private function getServiceDir()
    {
        return base_path() . '/app/Services/';
    }

    /**
     * Get the path to where we should store the service
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->getServiceDir() . ucfirst($this->getServiceName()) . '.php';
    }

    /**
     * Return the service name
     * 
     * @return mixed
     */
    protected function getServiceName()
    {
        return $this->argument('name');
    }

    /**
     * Get the entered repository
     *
     * @return string
     */
    protected function getRepository()
    {
        return $this->option('repo');
    }

    /**
     * Get the repository function name
     * 
     * @return string
     */
    protected function getRepositoryFunctionName()
    {
        return $this->option('func');
    }

    /**
     * Get the repository name from configuration
     *
     * @return string
     */
    protected function getRepositoryName()
    {
        return config('service-gen.repository_dir')
    }

    /**
     * Set a name for the function called by
     * the repository
     *
     * @return string
     */
    protected function configureRepositoryFunctionName()
    {
        $name = ucwords($this->getRepositoryFunctionName());

        return camel_case(str_replace('Service', '', $name));
    }
}