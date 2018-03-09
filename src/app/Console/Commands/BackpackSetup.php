<?php

namespace onethirtyone\backpacksetup\App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use onethirtyone\scifs\app\Classes\Http;
use Spatie\Permission\Models\Role;

/**
 * Class PortalInstall
 * @package App\Console\Commands
 */
class BackpackSetup extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backpack:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the backup user setup';


    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var User
     */
    protected $user;


    /**
     * Create a new command instance.
     *
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
        if(!$this->option('register')) {
            $this->getInputs();
            $this->createRoles();
            $this->createUser();
        } else {
            $this->user = User::first();
        }

        $this->registerApplication();
    }

    /**
     * Get user inputs
     */
    public function getInputs()
    {
        $this->name = $this->ask('Full name of Owner');
        $this->email = $this->ask('Email address of Owner');
        $this->password = $this->ask('Password of Owner Account');
        $this->info('Setting up user and roles');
    }

    /**
     *  Create Portal Roles
     */
    public function createRoles()
    {
        if(!Role::where('name', 'administrator')->exists()) {
            Role::create(['name' => 'administrator']);
        }

        if(!Role::where('name', 'owner')->exists()) {
            Role::create(['name' => 'owner']);
        }

        if(!Role::where('name', 'client')->exists()) {
            Role::create(['name' => 'client']);
        }
    }

    /**
     * @return User
     */
    public function createUser()
    {
        $this->user = User::firstOrNew([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->user->password = bcrypt($this->password);
        $this->user->save();

        if(!$this->user->hasRole('administrator')) {
            $this->user->assignRole('administrator');
        }

        return $this->user;
    }

    /**
     * Registers application
     */
    public function registerApplication()
    {
        if(!$this->user)
            return $this->error('Application not installed.  Please run portal:install first.');

        $this->info('Registering Application');
        $this->http->args([
            'name'     => $this->user->name,
            'email'    => $this->user->email,
            'referrer' => config('app.url'),
        ]);
        $response = $this->http->send('post', 'portal/register');

        switch ($response['status']) {
            case static::UNPROCESSABLE_ENTITY:
                $this->error('Could not register your application at this time.');
                break;
            case static::OK:
                $this->storeToken($response['body']['access_token']);
                $this->info('Application registered successfully');
                Cache::put('called_at', Carbon::now(), config('developer.cache'));
                break;
            default:
                $this->info($response['status']);
                break;
        }
    }

    /**
     * @param $token
     */
    public function storeToken($token)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        if(config('client.token')) {
            $oldValue = strtok($str, "SCIFS_TOKEN=");
            $str = str_replace("SCIFS_TOKEN={$oldValue}", "SCIFS_TOKEN={$token}\n", $str);
        } else {
            $str .= "\nSCIFS_TOKEN={$token}\n";
        }

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }

}
