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
        $this->createRoles();
        $this->createUser();
    }

    /**
     * Creates a new User
     */
    public function createUser()
    {
        User::create($this->getInputs())->assignRole('administrator');
    }

    /**
     * Get user inputs
     */
    public function getInputs()
    {
        return [
            'name' => $this->ask('Full name of Administrator'),
            'email' => $this->ask('Email address of Administrator'),
            'password' => bcrypt($this->ask('Password for Administrator Account')),
        ];
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
    }
}
