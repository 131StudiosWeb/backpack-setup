<?php

namespace onethirtyone\backpacksetup\App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
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
        if (!Role::where('name', 'administrator')->exists())
        {
            Role::create(['name' => 'administrator']);
        }

        if (!Role::where('name', 'owner')->exists())
        {
            Role::create(['name' => 'owner']);
        }

        if (!Role::where('name', 'client')->exists())
        {
            Role::create(['name' => 'client']);
        }
    }

    /**
     * @return User
     */
    public function createUser()
    {
        $this->user = User::firstOrNew([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->user->password = bcrypt($this->password);
        $this->user->save();

        if (!$this->user->hasRole('administrator'))
        {
            $this->user->assignRole('administrator');
        }

        return $this->user;
    }
}
